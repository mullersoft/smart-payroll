<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        return response()->json(BankAccount::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|unique:bank_accounts,account_number',
            'balance' => 'nullable|numeric',
            'employee_id' => 'nullable|exists:employees,id|unique:bank_accounts,employee_id',
            'owner_name' => 'nullable|string',
        ]);

        $data['balance'] = $data['balance'] ?? 0;

        if (!empty($data['employee_id']) && empty($data['owner_name'])) {
            $employee = Employee::findOrFail($data['employee_id']);
            $data['owner_name'] = $employee->full_name;
        }

        $account = BankAccount::create($data);

        return response()->json($account, 201);
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255|unique:bank_accounts,account_number,' . $bankAccount->id,
            'owner_name' => 'nullable|string|max:255',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        // Logic to correctly set owner_name based on employee_id
        if (!empty($validated['employee_id'])) {
            $employee = Employee::findOrFail($validated['employee_id']);
            $validated['owner_name'] = $employee->full_name;
        }

        $bankAccount->update($validated);
        return response()->json($bankAccount);
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();
        return response()->json(null, 204);
    }
}
