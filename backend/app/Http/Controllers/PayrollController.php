<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Services\PayrollService;
use Illuminate\Support\Carbon;
use App\Services\BankService;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     * Includes all payrolls.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(
            Payroll::with([
                'employee.position',
                'employee.employmentType',
                'allowances',
                'overtimes',
                'preparedBy',
                'approvedBy'
            ])->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PayrollService $payrollService)
    {
        if ($request->user()->role !== 'preparer') {
            return response()->json(['error' => 'Only preparers can create payrolls.'], 403);
        }

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'pay_month' => 'required|date',
            'working_days' => 'required|numeric|min:0|max:30',
            'other_commission' => 'nullable|numeric|min:0',
            'overtimes' => 'array',
            'overtimes.*.rate_type' => 'required|in:weekday_evening,night,rest_day,holiday',
            'overtimes.*.hours' => 'required|numeric|min:0',
        ]);

        $data['prepared_by'] = $request->user()->id;

        $payroll = $payrollService->generatePayroll($data);

        return response()->json(
            $payroll->load(['employee.position', 'employee.employmentType', 'allowances', 'overtimes', 'preparedBy']),
            201
        );
    }

    /**
     * Bulk payroll creation
     */
    public function bulkStore(Request $request, PayrollService $payrollService)
    {
        if ($request->user()->role !== 'preparer') {
            return response()->json(['error' => 'Only preparers can create payrolls.'], 403);
        }

        $data = $request->validate([
            'payrolls' => 'required|array|min:1',
            'payrolls.*.employee_id' => 'required|exists:employees,id',
            'payrolls.*.pay_month' => 'required|date',
            'payrolls.*.working_days' => 'required|numeric|min:0|max:30',
            'payrolls.*.other_commission' => 'nullable|numeric|min:0',
            'payrolls.*.overtimes' => 'array',
            'payrolls.*.overtimes.*.rate_type' => 'required|in:weekday_evening,night,rest_day,holiday',
            'payrolls.*.overtimes.*.hours' => 'required|numeric|min:0',
        ]);

        $createdPayrolls = [];

        foreach ($data['payrolls'] as $payrollData) {
            $payrollData['prepared_by'] = $request->user()->id;

            $createdPayrolls[] = $payrollService->generatePayroll($payrollData)
                ->load(['employee.position', 'employee.employmentType', 'allowances', 'overtimes', 'preparedBy']);
        }

        return response()->json([
            'message' => 'Payrolls created successfully.',
            'payrolls' => $createdPayrolls
        ], 201);
    }

    /**
     * Display a payroll.
     */
    public function show(Payroll $payroll)
    {
        return response()->json(
            $payroll->load([
                'employee.position',
                'employee.employmentType',
                'allowances',
                'overtimes',
                'transactions',
                'preparedBy',
                'approvedBy'
            ])
        );
    }

    /**
     * Update payroll.
     */
    public function update(Request $request, Payroll $payroll, PayrollService $payrollService)
    {
        if ($request->user()->role !== 'preparer') {
            return response()->json(['error' => 'Only preparers can update payrolls.'], 403);
        }

        if ($payroll->status !== 'prepared' && $payroll->status !== 'rejected') {
            return response()->json(['error' => 'Only prepared or rejected payrolls can be updated.'], 400);
        }

        $data = $request->validate([
            'working_days' => 'required|numeric|min:0|max:30',
            'other_commission' => 'nullable|numeric|min:0',
            'overtimes' => 'array',
            'overtimes.*.rate_type' => 'required|in:weekday_evening,night,rest_day,holiday',
            'overtimes.*.hours' => 'required|numeric|min:0',
        ]);

        $payroll->fill([
            'working_days' => $data['working_days'],
            'other_commission' => $data['other_commission'] ?? 0,
            'status' => 'prepared',
            'rejection_reason' => null,
            'prepared_by' => $request->user()->id,
        ]);

        $payroll = $payrollService->regeneratePayroll($payroll, $data['overtimes'] ?? []);

        return response()->json([
            'message' => 'Payroll updated successfully.',
            'payroll' => $payroll->load(['employee.position', 'employee.employmentType', 'allowances', 'overtimes', 'preparedBy'])
        ]);
    }

    /**
     * Delete payroll.
     */
    public function destroy(Request $request, Payroll $payroll)
    {
        if ($request->user()->role !== 'preparer') {
            return response()->json(['error' => 'Only preparers can delete payrolls.'], 403);
        }

        if ($payroll->status === 'approved') {
            return response()->json(['error' => 'Approved payrolls cannot be deleted.'], 400);
        }

        $payroll->delete();

        return response()->json(['message' => 'Payroll deleted successfully.']);
    }

    /**
     * Process payroll transaction.
     */
    public function processTransaction(Payroll $payroll, BankService $bankService)
    {
        if ($payroll->status !== 'approved') {
            return response()->json(['error' => 'Payroll must be approved before processing.'], 403);
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
     * Approve payroll.
     */
    public function approve(Payroll $payroll, Request $request)
    {
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
     * Reject payroll.
     */
    public function reject(Payroll $payroll, Request $request)
    {
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
        $payroll->save();

        return response()->json(['message' => 'Payroll rejected successfully.']);
    }

    /**
     * Pending payrolls.
     */
    public function pending()
    {
        $pendingPayrolls = Payroll::where('status', 'prepared')
            ->with(['employee.position', 'employee.employmentType', 'allowances', 'overtimes'])
            ->get();

        return response()->json([
            'count' => $pendingPayrolls->count(),
            'payrolls' => $pendingPayrolls
        ]);
    }

    /**
     * List payrolls with filters.
     */
    public function list(Request $request)
    {
        $query = Payroll::with(['employee.position', 'employee.employmentType', 'allowances', 'overtimes']);

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

    /**
     * Summary of approved payrolls.
     */
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
            $query->whereHas('employee.employmentType', function ($q) use ($request) {
                $q->where('name', $request->employment_type);
            });
        }

        $payrolls = $query->with('employee')->get();

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

    /**
     * Dashboard summary.
     */
    public function getDashboardSummary(Request $request)
    {
        $approvedPayrolls = Payroll::with('employee')
            ->where('status', 'approved')
            ->get();

        $totalExpenditure = $approvedPayrolls->sum('net_payment');
        $totalEmployees = Employee::count();

        $genderStats = Employee::selectRaw('gender, count(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender');

        $employmentTypeStats = Employee::selectRaw('employment_type_id, count(*) as count')
            ->groupBy('employment_type_id')
            ->pluck('count', 'employment_type_id');

        $positionStats = Employee::selectRaw('position_id, count(*) as count')
            ->groupBy('position_id')
            ->pluck('count', 'position_id');

        return response()->json([
            'totalExpenditure' => $totalExpenditure,
            'approvedPayrollCount' => $approvedPayrolls->count(),
            'totalEmployees' => $totalEmployees,
            'genderStats' => $genderStats,
            'employmentTypeStats' => $employmentTypeStats,
            'positionStats' => $positionStats,
        ]);
    }
}
