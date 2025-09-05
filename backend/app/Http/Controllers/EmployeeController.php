<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmployeeCreatedMail;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Allow only admin and preparer
        if (!in_array($user->role, ['admin', 'preparer'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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
// Store a new employee
public function store(Request $request)
{
    $user = auth()->user();

    // Only admin can create
    if ($user->role !== 'admin') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

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
        $data['employment_date'] = now()->toDateString();
    }

// Create employee
    $employee = Employee::create($data);
    $password = Str::random(10);
    $newUser = User::create([
        'name' => $data['full_name'],
        'email' => $data['email'],
        'password' => Hash::make($password),
        'role' => null, // not admin/preparer/approver
        'status' => 'deactivated',
    ]);

    // link user to employee
    $employee->update(['user_id' => $newUser->id]);

    // send email to employee
    Mail::to($newUser->email)->send(new EmployeeCreatedMail($newUser, $password));






// Filter and attach allowances
    if (!empty($data['allowances'])) {
        $allowances = array_filter($data['allowances'], fn($a) => !empty($a));
        $employee->allowances()->sync($allowances);
    }

    // Load only relevant relations/fields
    $employee->load([
        'position:id,name,description',
        'employmentType:id,name,description',
        'allowances:id,name,value'
    ]);

    return response()->json($employee, 201);
}
public function activate($id)
{
    $employee = Employee::findOrFail($id);
    $user = $employee->user;

    $user->status = 'active';
    $user->save();

    return response()->json(['message' => 'Employee activated successfully']);
}






// Update an existing employee
public function update(Request $request, $id)
{
    $user = auth()->user();

    // Only admin can update
    if ($user->role !== 'admin') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

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

    // Update employee
    $employee->update($data);

    // Filter and sync allowances
    if (isset($data['allowances'])) {
        $allowances = array_filter($data['allowances'], fn($a) => !empty($a));
        $employee->allowances()->sync($allowances);
    }

    // Load only relevant relations/fields
    $employee->load([
        'position:id,name,description',
        'employmentType:id,name,description',
        'allowances:id,name,value'
    ]);

    return response()->json($employee);
}

    public function show(Employee $employee)
    {
        $user = auth()->user();

        // Only admin can view details
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($employee->load(['bankAccount', 'position', 'employmentType', 'allowances']));
    }

    public function destroy($id)
    {
        $user = auth()->user();

        // ✅ Only admin can delete
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully']);
    }
    public function toggleStatus($id)
    {
        $user = auth()->user();

        // ✅ Only admin can toggle
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $employee = Employee::findOrFail($id);
        $employee->is_active = !$employee->is_active;
        $employee->save();

        return response()->json([
            'message' => 'Employee status updated',
            'is_active' => $employee->is_active
        ]);
    }
}
