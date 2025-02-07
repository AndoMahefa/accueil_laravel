<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function demandeVisiteursParDirection($idDirection)
    {
        $direction = Direction::findOrFail($idDirection);

        if (!$direction) {
            return response()->json(['message' => 'Direction non trouvé.'], 404);
        }

        $today = Carbon::now()->toDateString();
        // Récupérer les visiteurs avec un statut de 0
        $visiteurs = $direction->visiteurs()
            ->wherePivot('statut', 0)
            ->wherePivot('date_heure_arrivee', 'like', "$today%")
            ->get();

        return response()->json([
            'message' => 'Liste des visiteurs avec statut 0.',
            'visiteurs' => $visiteurs,
        ], 200);
    }

    public function demandeVisiteurs() {
        $today = Carbon::today();
        $visiteurs = DB::table('visiteur_service')
            ->join('visiteur', 'visiteur_service.id_visiteur', '=', 'visiteur.id')
            ->join('direction', 'visiteur_service.id_direction', '=', 'direction.id')
            ->where('visiteur_service.statut', 0)
            ->whereDate('visiteur_service.date_heure_arrivee', '>=', $today)
            ->select('visiteur.*', 'visiteur_service.*', 'direction.nom as nom_direction') // Sélectionne les colonnes des deux tables
            ->get();

        return response()->json([
            'message' => 'Liste de tous les visiteurs',
            'visiteurs' => $visiteurs
        ]);
    }

    public function findJour($idJour) {
        $jour = DB::table('jour')->where('id', $idJour)->get();

        return response()->json($jour);
    }
}
