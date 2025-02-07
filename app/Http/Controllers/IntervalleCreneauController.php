<?php

namespace App\Http\Controllers;

use App\Models\IntervalleCreneau;
use Illuminate\Http\Request;

class IntervalleCreneauController extends Controller
{
    //
    public function getIntervalles() {
        $intervalles = IntervalleCreneau::all();

        return response()->json([
            'message' => 'Tous les intervalles de temps',
            'intervalles' => $intervalles
        ]);
    }

    public function store(Request $request) {
        $donnees = $request->validate([
            'intervalle' => 'required|int',
            'id_direction' => 'required|int|exists:direction,id',
            'id_service' => 'nullable|int|exists:service,id'
        ]);

        $intervalle = IntervalleCreneau::create($donnees);

        return response()->json([
            'message' => 'Intervalle creer avec succes',
            'intervalle' => $intervalle
        ]);
    }

    public function update(Request $request, $idIntervalle) {
        $donnees = $request->validate([
            'intervalle' => 'required|int',
            'id_direction' => 'required|int|exists:direction,id',
            'id_service' => 'nullable|int|exists:service,id'
        ]);

        $intervalle = IntervalleCreneau::findOrFail($idIntervalle);
        $intervalle->update($donnees);

        return response()->json([
            'message' => 'Intervalle à jour avec succes',
            'intervalle' => $intervalle
        ]);
    }

    public function findByDirection($idDirection) {
        $intervalle = IntervalleCreneau::findOrFail($idDirection);

        return response()->json([
            'message' => 'Intervalle trouvé',
            'intervalle' => $intervalle
        ]);
    }

    public function findByService($idService) {
        $intervalle = IntervalleCreneau::findOrFail($idService);

        return response()->json([
            'message' => 'Intervalle trouvé',
            'intervalle' => $intervalle
        ]);
    }
}
