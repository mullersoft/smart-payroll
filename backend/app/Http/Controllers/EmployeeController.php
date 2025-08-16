<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['bankAccount', 'position', 'employmentType']);

        if ($request->has('is_active')) {
            $isActive = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
            $query->where('is_active', $isActive);
        }

        if ($request->has('search')) {
            $query->where('full_name', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|string|max:50|unique:employees',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'employment_date' => 'required|date',
            'position_id' => 'required|exists:positions,id',
            'employment_type_id' => 'required|exists:employment_types,id',
            'base_salary' => 'required|numeric|min:0',
        ]);

        $employee = Employee::create($data);

        return response()->json($employee->load(['position', 'employmentType']), 201);
    }

    public function show(Employee $employee)
    {
        return response()->json($employee->load(['bankAccount', 'position', 'employmentType']));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'employee_id' => 'required|string|max:50|unique:employees,employee_id,' . $employee->id,
            'position_id' => 'required|exists:positions,id',
            'employment_type_id' => 'required|exists:employment_types,id',
            'base_salary' => 'required|numeric|min:0',
            'gender' => 'required|string|max:20',
            'employment_date' => 'required|date',
        ]);

        $employee->update($data);

        return response()->json($employee->load(['position', 'employmentType']));
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully']);
    }

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
