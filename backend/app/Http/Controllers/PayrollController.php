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
    public function index()
    {
        return response()->json(Payroll::with('employee')->get());
    }

    public function store(Request $request, PayrollService $payrollService)
    {
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
     * ✅ Bulk payroll creation
     */
    public function bulkStore(Request $request, PayrollService $payrollService)
    {
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

    public function show(Payroll $payroll)
    {
        return response()->json($payroll->load('transactions'));
    }

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

    public function approve(Payroll $payroll, Request $request)
    {
        if ($payroll->status !== 'prepared') {
            return response()->json(['error' => 'Only prepared payrolls can be approved.'], 400);
        }

        $payroll->status = 'approved';
        $payroll->approved_by = $request->user()->id;
        $payroll->save();

        return response()->json(['message' => 'Payroll approved successfully.']);
    }

    public function pending()
    {
        $pendingPayrolls = Payroll::where('status', 'prepared')->with('employee')->get();

        return response()->json([
            'count' => $pendingPayrolls->count(),
            'payrolls' => $pendingPayrolls
        ]);
    }

    public function reject(Payroll $payroll, Request $request)
    {
        if ($payroll->status !== 'prepared') {
            return response()->json(['error' => 'Only prepared payrolls can be rejected.'], 400);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $payroll->status = 'rejected';
        $payroll->rejection_reason = $request->rejection_reason;
        $payroll->rejected_at = Carbon::now();
        $payroll->approved_by = Auth::user()->id;
        $payroll->save();

        return response()->json(['message' => 'Payroll rejected successfully.']);
    }

    public function update(Request $request, Payroll $payroll, PayrollService $payrollService)
    {
        if ($payroll->status !== 'rejected') {
            return response()->json(['error' => 'Only rejected payrolls can be updated.'], 400);
        }

        $data = $request->validate([
            'working_days' => 'required|numeric|min:0|max:30',
            'other_commission' => 'nullable|numeric|min:0',
            'prepared_by' => 'required|exists:users,id',
        ]);

        $payroll->fill($data);
        $payroll = $payrollService->regeneratePayroll($payroll);
        
        return response()->json(['message' => 'Payroll updated and ready for re-approval.', 'payroll' => $payroll]);
    }
}
