<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        // return response()->json(Position::all());
        $positions = Position::select('id', 'name', 'description', 'allowance', 'type', 'is_taxable')->get();
        return response()->json($positions);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:positions',
            'description' => 'nullable|string',
            'allowance' => 'required|numeric|min:0',
            'type' => 'required|in:fixed,percent',
        'is_taxable' => 'required|boolean',

        ]);
        $position = Position::create($data);

        // return response()->json($position, 201);
        return response()->json($position->only([
            'id', 'name', 'description', 'allowance', 'type', 'is_taxable'
        ]), 201);
    }

    public function update(Request $request, Position $position)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:positions,name,' . $position->id,
            'description' => 'nullable|string',
            'allowance' => 'required|numeric|min:0',
            'type' => 'required|in:fixed,percent',
             'is_taxable' => 'required|boolean',
        ]);
        $position->update($data);
        // return response()->json($position);
        // use  Laravel’s only() method to filter none relevant fields
        return response()->json($position->only([
                   'id', 'name', 'description', 'allowance', 'type', 'is_taxable'

        ]));
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return response()->json(['message' => 'Position deleted']);
    }
}
