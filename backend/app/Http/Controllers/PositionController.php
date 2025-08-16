<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        return response()->json(Position::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:positions',
            'description' => 'nullable|string',

        ]);
        $position = Position::create($data);

        return response()->json($position, 201);
    }

    public function update(Request $request, Position $position)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:positions,name,' . $position->id,
            'description' => 'nullable|string',
        ]);
        $position->update($data);
        return response()->json($position);
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return response()->json(['message' => 'Position deleted']);
    }
}
