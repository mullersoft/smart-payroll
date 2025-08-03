<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return response()->json(Employee::with('bankAccount')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|unique:employees',
            'full_name' => 'required|string',
            'gender' => 'required|in:Male,Female',
            'employment_date' => 'required|date',
            'position' => 'required|string',
            'employment_type' => 'required|in:permanent,contract',
            'base_salary' => 'required|numeric',
            // 'bank_account' => 'nullable|exists:bank_accounts,id',
        ]);

        $employee = Employee::create($data);
        return response()->json($employee, 201);
    }

    public function show(Employee $employee)
    {
        return response()->json($employee->load('bankAccount'));
    }
}
