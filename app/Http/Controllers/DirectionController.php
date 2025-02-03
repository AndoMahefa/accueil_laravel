<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use Illuminate\Http\Request;

class DirectionController extends Controller
{
    public function getDirections() {
        $directions = Direction::all();
        return response()->json([
            'status' => 'success',
            'directions' => $directions
        ]);
    }

    public function store(Request $request) {
        $donnees = $request->validate([
            'nom' => 'required|string|max:100',
            'id_parent_dir' => 'nullable|int|exists:direction,id'
        ]);

        $direction = Direction::create($donnees);
        return response()->json([
            'status' => 'success',
            'direction' => $direction
        ], 201);
    }

    public function update(Request $request, $id) {
        $donnee = $request->validate([
            'nom' => 'required|string|max:100'
        ]);

        $direction = Direction::find($id);
        $direction->update($donnee);

        return response()->json([
            'status' => 'success',
            'direction' => $direction
        ]);
    }

    public function delete($id) {
        $direction = Direction::find($id);
        $direction->delete();
        return response()->json([
            'status' => 'success'
        ], 204);
    }
}
