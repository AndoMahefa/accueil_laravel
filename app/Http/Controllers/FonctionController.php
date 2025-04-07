<?php

namespace App\Http\Controllers;

use App\Models\Fonction;
use Illuminate\Http\Request;

class FonctionController extends Controller {
    public function getFonctions() {
        $fonctions = Fonction::with(['service', 'direction'])->paginate(10);

        return response()->json([
            'status' => 'success',
            'fonctions' => $fonctions
        ]);
    }

    public function store(Request $request) {
        $donnees = $request->validate([
            'nom' => 'required|string|max:100',
            'id_service' => 'nullable|integer|exists:service,id',
            'id_direction' => 'required|integer|exists:direction,id'
        ]);

        $fonction = Fonction::create($donnees);
        return response()->json([
            'status' => 'success',
            'fonction' => $fonction
        ], 201);
    }

    public function update(Request $request, $id) {
        $donnee = $request->validate([
            'nom' => 'required|string|max:100',
            'id_service' => 'required|integer',
            'id_direction' => 'required|integer'
        ]);

        $fonction = Fonction::find($id);
        $fonction->update($donnee);
        return response()->json([
            'status' => 'success',
            'fonction' => $fonction
        ], 200);
    }

    public function delete($id) {
        $fonction = Fonction::find($id);
        $fonction->delete();
        return response()->json([
            'status' => 'success'
        ], 204);
    }

    public function getFonctionsByService($idService) {
        $fonctions = Fonction::where('id_service', $idService)->get();
        return response()->json([
            'status' => 'success',
            'fonctions' => $fonctions
        ]);
    }

    public function getFonctionsByDirection($idDirection) {
        $fonctions = Fonction::where('id_direction', $idDirection)->get();
        return response()->json([
            'status' => 'success',
            'fonctions' => $fonctions
        ]);
    }
}
