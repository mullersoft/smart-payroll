<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get(
            'status',
            'completed'
        ); // default is completed

        return response()->json(
            Transaction::with([
                'payroll.employee',
                'fromBankAccount',
                'toBankAccount'
            ])
                ->when($status, function ($query, $status) {
                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    public function show($id)
    {
        return response()->json(Transaction::with('payroll')->findOrFail($id));
    }
    public function myTransactions(Request $request)
{
    $user = $request->user();

$employee = $user->employee;
if (!$employee) {
    return response()->json(['error' => 'Not linked to an employee'], 403);
}

$transactions = Transaction::whereHas('payroll', function ($q) use ($employee) {
    $q->where('employee_id', $employee->id);
})

        ->with(['payroll'])
        ->orderBy('transaction_date', 'desc')
        ->get();

    return response()->json($transactions);
}

}
