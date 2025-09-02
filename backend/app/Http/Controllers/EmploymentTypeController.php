<?php

namespace App\Http\Controllers;

use App\Models\EmploymentType;
use Illuminate\Http\Request;

class EmploymentTypeController extends Controller
{
    public function index()
    {
        // return response()->json(EmploymentType::all());
        $employmentTypes = EmploymentType::select('id', 'name', 'description')->get();
        return response()->json($employmentTypes);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:employment_types',
            'description' => 'nullable|string',
        ]);
        $type = EmploymentType::create($data);
        // return response()->json($type, 201);
        return response()->json($type->only([
            'id',
            'name',
            'description'
        ]), 201);
    }

    public function update(Request $request, EmploymentType $employmentType)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:employment_types,name,' . $employmentType->id,
            'description' => 'nullable|string',
        ]);
        $employmentType->update($data);
        // return response()->json($employmentType);
        // use  Laravel’s only() method to filter none relevant fields
        return response()->json($employmentType->only([
            'id',
            'name',
            'description'
        ]));
    }

    public function destroy(EmploymentType $employmentType)
    {
        $employmentType->delete();
        return response()->json(['message' => 'Employment Type deleted']);
    }
}
