<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Services\PayrollService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     * Includes all payrolls.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // return response()->json(Payroll::with('employee')->get());// to display the name on the position in payroll
        return response()->json(Payroll::with('employee.position')->get());
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param PayrollService $payrollService
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, PayrollService $payrollService)
    {
        // Only users with the 'preparer' role can create payrolls
        if ($request->user()->role !== 'preparer') {
            return response()->json(['error' => 'Only preparers can create payrolls.'], 403);
        }

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'pay_month' => 'required|date',
            'working_days' => 'required|numeric|min:0|max:30',
            'other_commission' => 'nullable|numeric|min:0',
        ]);

        $data['prepared_by'] = $request->user()->id;

        $payroll = $payrollService->generatePayroll($data);

        return response()->json($payroll, 201);
    }

    /**
     * Bulk payroll creation
     * @param Request $request
     * @param PayrollService $payrollService
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkStore(Request $request, PayrollService $payrollService)
    {
        // Only users with the 'preparer' role can bulk create payrolls
        if ($request->user()->role !== 'preparer') {
            return response()->json(['error' => 'Only preparers can create payrolls.'], 403);
        }

        $data = $request->validate([
            'payrolls' => 'required|array|min:1',
            'payrolls.*.employee_id' => 'required|exists:employees,id',
            'payrolls.*.pay_month' => 'required|date',
            'payrolls.*.working_days' => 'required|numeric|min:0|max:30',
            'payrolls.*.other_commission' => 'nullable|numeric|min:0',
        ]);

        $createdPayrolls = [];

        foreach ($data['payrolls'] as $payrollData) {
            $payrollData['prepared_by'] = $request->user()->id;
            $createdPayrolls[] = $payrollService->generatePayroll($payrollData);
        }

        return response()->json([
            'message' => 'Payrolls created successfully.',
            'payrolls' => $createdPayrolls
        ], 201);
    }

    /**
     * Display the specified resource.
     * @param Payroll $payroll
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Payroll $payroll)
    {
        return response()->json($payroll->load('transactions'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Payroll $payroll
     * @param PayrollService $payrollService
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Payroll $payroll, PayrollService $payrollService)
    {
        // Only users with the 'preparer' role can update payrolls
        if ($request->user()->role !== 'preparer') {
            return response()->json(['error' => 'Only preparers can update payrolls.'], 403);
        }

        // Only payrolls with 'prepared' or 'rejected' status can be updated
        if ($payroll->status !== 'prepared' && $payroll->status !== 'rejected') {
            return response()->json(['error' => 'Only prepared or rejected payrolls can be updated.'], 400);
        }

        $data = $request->validate([
            'working_days' => 'required|numeric|min:0|max:30',
            'other_commission' => 'nullable|numeric|min:0',
        ]);

        // Update the payroll data and regenerate the payroll
        $payroll->fill([
            'working_days' => $data['working_days'],
            'other_commission' => $data['other_commission'] ?? 0,
            'status' => 'prepared', // Reset status to prepared after modification
            'rejection_reason' => null, // Clear rejection reason if updated
            'prepared_by' => $request->user()->id,
        ]);

        $payroll = $payrollService->regeneratePayroll($payroll);

        return response()->json(['message' => 'Payroll updated successfully.', 'payroll' => $payroll]);
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param Payroll $payroll
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Payroll $payroll)
    {
        // Only users with the 'preparer' role can delete payrolls
        if ($request->user()->role !== 'preparer') {
            return response()->json(['error' => 'Only preparers can delete payrolls.'], 403);
        }

        // Prevent deletion of approved payrolls
        if ($payroll->status === 'approved') {
            return response()->json(['error' => 'Approved payrolls cannot be deleted.'], 400);
        }

        $payroll->delete();

        return response()->json(['message' => 'Payroll deleted successfully.']);
    }

    /**
     * Process a payroll transaction.
     * @param Payroll $payroll
     * @param \App\Services\BankService $bankService
     * @return \Illuminate\Http\JsonResponse
     */
    public function processTransaction(Payroll $payroll, \App\Services\BankService $bankService)
    {
        if ($payroll->status !== 'approved') {
            return response()->json([
                'error' => 'Payroll must be approved before processing.'
            ], 403);
        }

        try {
            $payroll->load('employee');
            $bankService->processPayrollTransaction($payroll);
            return response()->json(['message' => 'Transaction processed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Approve a payroll.
     * @param Payroll $payroll
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve(Payroll $payroll, Request $request)
    {
        // Only users with the 'approver' role can approve payrolls
        if ($request->user()->role !== 'approver') {
            return response()->json(['error' => 'Only approvers can approve payrolls.'], 403);
        }

        if ($payroll->status !== 'prepared') {
            return response()->json(['error' => 'Only prepared payrolls can be approved.'], 400);
        }

        $payroll->status = 'approved';
        $payroll->approved_by = $request->user()->id;
        $payroll->save();

        return response()->json(['message' => 'Payroll approved successfully.']);
    }

    /**
     * Get pending payrolls.
     * @return \Illuminate\Http\JsonResponse
     */
    public function pending()
    {
        $pendingPayrolls = Payroll::where('status', 'prepared')->with('employee')->get();

        return response()->json([
            'count' => $pendingPayrolls->count(),
            'payrolls' => $pendingPayrolls
        ]);
    }

    /**
     * Reject a payroll.
     * @param Payroll $payroll
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject(Payroll $payroll, Request $request)
    {
        // Only users with the 'approver' role can reject payrolls
        if ($request->user()->role !== 'approver') {
            return response()->json(['error' => 'Only approvers can reject payrolls.'], 403);
        }

        if ($payroll->status !== 'prepared') {
            return response()->json(['error' => 'Only prepared payrolls can be rejected.'], 400);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $payroll->status = 'rejected';
        $payroll->rejection_reason = $request->rejection_reason;
        $payroll->rejected_at = Carbon::now();
        // Keep approved_by unchanged or null; rejection is not an approval
        $payroll->save();

        return response()->json(['message' => 'Payroll rejected successfully.']);
    }

    /**
     * Get a list of payrolls with optional filters.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $query = Payroll::with('employee');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('month')) {
            $query->where('pay_month', 'like', $request->month . '%');
        }

        return response()->json([
            'payrolls' => $query->orderBy('pay_month', 'desc')->get()
        ]);
    }
    public function summary(Request $request)
    {
        $query = Payroll::where('status', 'approved');

        if ($request->filled('year')) {
            $query->whereYear('pay_month', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('pay_month', $request->month);
        }

        if ($request->filled('employment_type')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('employment_type', $request->employment_type);
            });
        }

        $payrolls = $query->get();

        // Group by month
        $grouped = $payrolls->groupBy(function ($item) {
            return $item->pay_month->format('Y-m');
        });

        $result = $grouped->map(function ($group) {
            return [
                'month' => $group->first()->pay_month->format('F Y'),
                'employee_count' => $group->count(),
                'total_gross_pay' => $group->sum('gross_pay'),
                'total_taxes' => $group->sum('income_tax'),
                'total_pension' => $group->sum('employee_pension') + $group->sum('employer_pension'),
                'total_net_pay' => $group->sum('net_payment')
            ];
        })->values();

        return response()->json($result);
    }
    // D:\qelem meda\smart-payroll\backend\app\Http\Controllers\PayrollController.php

    // ... other methods ...

    /**
     * Get summary statistics for the dashboard.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardSummary(Request $request)
    {
        // Fetch all approved payrolls
        $approvedPayrolls = Payroll::with('employee')
            ->where('status', 'approved')
            ->get();

        // Get total expenditure (sum of net_payment for approved payrolls)
        $totalExpenditure = $approvedPayrolls->sum('net_payment');

        // Get total number of employees
        $totalEmployees = Employee::count();

        // Calculate gender statistics from employee data
        $genderStats = Employee::selectRaw('gender, count(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender');

        // Calculate employment type statistics from employee data
        $employmentTypeStats = Employee::selectRaw('employment_type, count(*) as count')
            ->groupBy('employment_type')
            ->pluck('count', 'employment_type');

        // Calculate position statistics from employee data
        $positionStats = Employee::selectRaw('position, count(*) as count')
            ->groupBy('position')
            ->pluck('count', 'position');


        return response()->json([
            'totalExpenditure' => $totalExpenditure,
            'approvedPayrollCount' => $approvedPayrolls->count(),
            'totalEmployees' => $totalEmployees,
            'genderStats' => $genderStats,
            'employmentTypeStats' => $employmentTypeStats,
            'positionStats' => $positionStats,
        ]);
    }

    // ... other methods ...
}
