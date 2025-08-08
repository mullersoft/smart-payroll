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
        'account_number' => 'required|unique:bank_accounts',
        'balance' => 'nullable|numeric',
        'employee_id' => 'nullable|exists:employees,id',
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


 
}
