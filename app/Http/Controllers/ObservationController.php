<?php

namespace App\Http\Controllers;

use App\Models\Observation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ObservationController extends Controller
{
    public function getObservations() {
        Log::info(Observation::all());
        return Observation::all();
    }

    public function store(Request $request) {
        $donnees = $request->validate([
            'observation' => 'required|string|max:255'
        ]);

        $observation = Observation::create($donnees);
        return response()->json([
            'status' => 'success',
            'observation' => $observation
        ], 201);
    }

    public function update(Request $request, $id) {
        $donnee = $request->validate([
            'observation' => 'required|string|max:255'
        ]);

        $observation = Observation::find($id);
        $observation->update($donnee);
        return response()->json([
            'status' => 'success',
            'observation' => $observation
        ], 200);
    }

    public function delete($id) {
        $observation = Observation::find($id);
        $observation->delete();
        return response()->json([
            'status' => 'success'
        ], 204);
    }
}
