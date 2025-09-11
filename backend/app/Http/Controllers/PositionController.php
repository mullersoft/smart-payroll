<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        // return response()->json(Position::all());
        $positions = Position::select('id', 'name', 'description', 'allowance')->get();
        return response()->json($positions);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:positions',
            'description' => 'nullable|string',
            'allowance' => 'required|numeric|min:0',

        ]);
        $position = Position::create($data);

        // return response()->json($position, 201);
        return response()->json($position->only([
            'id',
            'name',
            'description',
            'allowance'
        ]), 201);
    }

    public function update(Request $request, Position $position)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:positions,name,' . $position->id,
            'description' => 'nullable|string',
            'allowance' => 'required|numeric|min:0', // Changed from 'number' to 'numeric'
        ]);
        $position->update($data);
        // return response()->json($position);
        // use  Laravel’s only() method to filter none relevant fields
        return response()->json($position->only([
            'id',
            'name',
            'description',
            'allowance'
        ]));
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return response()->json(['message' => 'Position deleted']);
    }
}
