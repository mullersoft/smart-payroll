<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Services\PayrollService;

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
            'prepared_by' => 'required|exists:users,id',
        ]);

        $payroll = $payrollService->generatePayroll($data);
        return response()->json($payroll, 201);
    }

    public function show(Payroll $payroll)
    {
        return response()->json($payroll->load('transactions'));
    }

public function processTransaction(Payroll $payroll, \App\Services\BankService $bankService)
{
    try {
        $payroll->load('employee'); // 👈 Important!
        $bankService->processPayrollTransaction($payroll);
        return response()->json(['message' => 'Transaction processed successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
}


}
