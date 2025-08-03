<?php
namespace App\Http\Controllers;

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
        'account_number' => 'required|unique:bank_accounts',
        'balance' => 'nullable|numeric', // 👈 allow missing value
        'employee_id' => 'nullable|exists:employees,id',
        'owner_name' => 'nullable|string',
    ]);
    // 👇 Assign balance = 0 if not set
    $data['balance'] = $data['balance'] ?? 0;
    // 🔁 Auto-fill owner_name if linked to employee
    if ($data['employee_id'] && empty($data['owner_name'])) {
        $employee = \App\Models\Employee::findOrFail($data['employee_id']);
        $data['owner_name'] = $employee->full_name;
    }

    $account = \App\Models\BankAccount::create($data);

    return response()->json($account, 201);
}

 
}
