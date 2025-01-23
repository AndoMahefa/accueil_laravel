<?php

namespace App\Http\Controllers;

use App\Models\RemiseOffre;
use App\Models\Soumissionaire;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RemiseOffreController extends Controller
{
    public function store(Request $request) {
        $donnees = $request->validate([
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string',
            'entreprise' => 'required|string',
            'nif_stat' => 'required|string',
            'adresse_siege' => 'required|string',
            'contact' => 'required|string',
            'rcs' => 'required|string',
            'fiscale' => 'required|string',
            'id_appel_offre' => 'required|exists:appel_offre_table,id'
        ]);

        $soumissionaire = Soumissionaire::insertGetId(
            [
                'nom' => $donnees['nom'],
                'prenom' => $donnees['prenom'],
                'entreprise' => $donnees['entreprise'],
                'nif_stat' => $donnees['nif_stat'],
                'adresse_siege' => $donnees['adresse_siege'],
                'contact' => $donnees['contact'],
                'rcs' => $donnees['rcs'],
                'fiscale' => $donnees['fiscale']
            ]
        );

        $now = Carbon::now();

        $remiseOffre = RemiseOffre::create([
            'date_remise' => $now->toDateString(),
            'heure_remise' => $now->toTimeString(),
            'id_soumissionaire' => $soumissionaire,
            'id_appel_offre' => $donnees['id_appel_offre']
        ]);

        return response()->json([
            'message' => 'Remise d\'offre créée avec succès',
            'remise_offre' => $remiseOffre,
        ]);
    }

    public function allRemiseOffres() {
        return response()->json(RemiseOffre::with('soumissionaire')->get());
    }
}

