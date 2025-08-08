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
            'employee_id' => 'required|string|max:50|unique:employees',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'employment_date' => 'required|date',
            'position' => 'required|string|in:CEO,COO,CTO,CISO,Director,Dept Lead,Normal Employee',
            'employment_type' => 'required|in:permanent,contract',
            'base_salary' => 'required|numeric|min:0',
        ]);

        $employee = Employee::create($data);
        return response()->json($employee, 201);
    }

    public function show(Employee $employee)
    {
        return response()->json($employee->load('bankAccount'));
    }
    // 📌 PUT /employees/{id}
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'employee_id' => 'required|string|max:50|unique:employees,employee_id,' . $employee->id,
            'position' => 'required|in:CEO,COO,CTO,CISO,Director,Dept Lead,Normal Employee',
            'employment_type' => 'required|in:permanent,contract',
            'base_salary' => 'required|numeric|min:0',
            'gender' => 'required|string|max:20',
            'employment_date' => 'required|date',
        ]);

        $employee->update($data);

        return response()->json($employee);
    }
  //  DELETE /employees/{id}
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully']);
    }
    //  POST /employees/{id}/toggle-status
    public function toggleStatus($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->is_active = !$employee->is_active;
        $employee->save();

        return response()->json([
            'message' => 'Employee status updated',
            'is_active' => $employee->is_active
        ]);
    }
}
