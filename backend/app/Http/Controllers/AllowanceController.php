<?php

namespace App\Http\Controllers;

use App\Models\Allowance;
use Illuminate\Http\Request;

class AllowanceController extends Controller
{
    /**
     * Display a listing of the allowances.
     */
    public function index()
    {
        // return response()->json(Allowance::all());
        $allowances = Allowance::select('id', 'name', 'type', 'value', 'is_taxable', 'description')->get();
        return response()->json($allowances);
    }

    /**
     * Store a newly created allowance.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:allowances,name',
            'type'        => 'required|in:fixed,percent',
            'value'       => 'required|numeric|min:0',
            'is_taxable'  => 'required|boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $allowance = Allowance::create($data);

        // return response()->json($allowance, 201);
        return response()->json($allowance->only([
            'id',
            'name',
            'type',
            'value',
            'is_taxable',
            'description'
        ]), 201);
    }

    /**
     * Display the specified allowance.
     */
    public function show(Allowance $allowance)
    {
        return response()->json($allowance);
    }

    /**
     * Update the specified allowance.
     */
    public function update(Request $request, Allowance $allowance)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:allowances,name,' . $allowance->id,
            'type'        => 'required|in:fixed,percent',
            'value'       => 'required|numeric|min:0',
            'is_taxable'  => 'required|boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $allowance->update($data);


        // return response()->json($allowance);
        // use  Laravel’s only() method to filter none relevant fields
        return response()->json($allowance->only([
            'id',
            'name',
            'type',
            'value',
            'is_taxable',
            'description'
        ]));
    }

    /**
     * Remove the specified allowance.
     */
    public function destroy(Allowance $allowance)
    {
        $allowance->delete();
        return response()->json(['message' => 'Allowance deleted successfully']);
    }
}
