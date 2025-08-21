<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['bankAccount', 'position', 'employmentType', 'allowances']);

        if ($request->has('is_active')) {
            $isActive = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
            $query->where('is_active', $isActive);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'gender' => 'required|in:Male,Female',
            'employment_date' => 'nullable|date',
            'position_id' => 'required|exists:positions,id',
            'employment_type_id' => 'required|exists:employment_types,id',
            'base_salary' => 'required|numeric|min:0',
            'allowances' => 'array',
        ]);

        // Auto-generate employee_id
        $lastEmployee = Employee::latest('id')->first();
        $nextId = $lastEmployee ? $lastEmployee->id + 1 : 1;
        $data['employee_id'] = 'emp' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        // Default employment date if not provided
        if (empty($data['employment_date'])) {
            $data['employment_date'] = Carbon::now()->toDateString();
        }

        $employee = Employee::create($data);

        // Attach allowances if provided
        if ($request->has('allowances')) {
            $employee->allowances()->sync($request->allowances);
        }

        return response()->json($employee->load(['position', 'employmentType', 'allowances']), 201);
    }

    public function show(Employee $employee)
    {
        return response()->json($employee->load(['bankAccount', 'position', 'employmentType', 'allowances']));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'position_id' => 'required|exists:positions,id',
            'employment_type_id' => 'required|exists:employment_types,id',
            'base_salary' => 'required|numeric|min:0',
            'gender' => 'required|string|max:20',
            'employment_date' => 'required|date',
            'allowances' => 'array',
        ]);

        $employee->update($data);

        // Sync allowances
        if ($request->has('allowances')) {
            $employee->allowances()->sync($request->allowances);
        }

        return response()->json($employee->load(['position', 'employmentType', 'allowances']));
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
