<?php

namespace App\Http\Controllers;

use App\Models\Pointage;
use App\Models\Statut;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;

class PointageController extends Controller
{
    public function pointerArrivee(Request $request) {
        // Validation des données
        $request->validate([
            'id_employe' => 'required|int',
            'heure_arrivee' => 'nullable'
        ]);

        // Récupérer la date et l'heure actuelles
        $currentDate = Carbon::now()->toDateString();
        // $currentTime = Carbon::now()->toTimeString();
        $currentTime = null;
        if($request['heure_arrivee']) {
            $currentTime = $request['heure_arrivee'];
        } else {
            $currentTime = Carbon::now()->toTimeString();
        }

        // Vérifier les pointages existants pour l'employé le même jour
        $lastPointage = Pointage::where('id_employe', $request->id_employe)
            ->whereDate('date', $currentDate)
            ->orderBy('session', 'desc')
            ->first();


        // Déterminer la nouvelle session
        $session = $lastPointage ? $lastPointage->session + 1 : 1;

        if(!$lastPointage || ($lastPointage && $lastPointage->heure_depart != null)) {
            // Créer un nouveau pointage
            $pointage = Pointage::create([
                'date' => $currentDate,
                'heure_arrivee' => $currentTime,
                'session' => $session,
                'id_employe' => $request->id_employe
            ]);


            return response()->json(['message' => 'Pointage enregistré avec succès.', 'pointage'=>$pointage->refresh()], 200);
        }

        return response()->json(['message'=>'L\'employé n\'est pas encore sorti du boulot',], 404);
    }

    public function pointerDepart(Request $request) {
        // Validation des données
        $request->validate([
            'id_employe' => 'required|int',
            'heure_depart' => 'nullable'
        ]);

        $currentDate = Carbon::now()->toDateString();

        $currentTime = null;
        if($request['heure_depart']) {
            $currentTime = $request['heure_depart'];
        } else {
            $currentTime = Carbon::now()->toTimeString();
        }

        // Vérifier les pointages existants pour l'employé le même jour
        $lastPointage = Pointage::where('id_employe', $request->id_employe)
            ->whereDate('date', $currentDate)
            ->orderBy('session', 'desc')
            ->first();

        if ($lastPointage && $lastPointage->heure_depart == null) {
            // Mettre à jour l'heure de départ
            $lastPointage->heure_depart = $currentTime;
            $lastPointage->save(); // Enregistrer les modifications

            return response()->json(['message' => 'Heure de départ mise à jour avec succès.', 'pointage' => $lastPointage], 200);
        }

        return response()->json(['message' => 'Aucun pointage trouvé pour cet employé aujourd\'hui.'], 404);
    }

    public function pointerConge(Request $request) {
        // Validation des données
        $donnees = $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'id_employe' => 'required|integer|exists:employe,id',
        ]);

        // Récupération du statut "Congé"
        $statutConge = Statut::where('statut', 'Congé')->first();
        Log::info("Statut : " . $statutConge);
        if (!$statutConge) {
            return response()->json(['message' => 'Statut congé non configuré'], 400);
        }

        // Création de la période
        $periode = CarbonPeriod::create(
            Carbon::parse($donnees['date_debut']),
            Carbon::parse($donnees['date_fin'])
        );

        $congesCrees = [];

        foreach ($periode as $date) {
            // Vérification des doublons
            $existeDeja = Pointage::where([
                'id_employe' => $donnees['id_employe'],
                'date' => $date->format('Y-m-d'),
            ])->exists();

            if (!$existeDeja) {
                $congesCrees[] = Pointage::create([
                    'date' => $date,
                    'session' => 1,
                    'id_employe' => $donnees['id_employe'],
                    'id_statut' => $statutConge->id
                ]);
            }
        }

        return response()->json([
            'message' => 'Congés enregistrés avec succès',
            'jours_crees' => count($congesCrees),
            'details' => $congesCrees
        ], 201);
    }

    public function pointerPermission(Request $request) {

        $statut = Statut::where('statut', 'Permission')->first();

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'heure_arrivee' => 'nullable|date_format:H:i',
            'heure_depart' => 'nullable|date_format:H:i',
            'id_employe' => 'required|integer|exists:employe,id'
        ]);

        // Si c'est une permission pour une journée entière
        if (empty($request->heure_arrivee) && empty($request->heure_depart)) {
            $request->merge([
                'heure_arrivee' => '00:00',
                'heure_depart' => '23:59',
            ]);
        }

        // Création de la permission
        $pointage = Pointage::create([
            'date' => $request->date,
            'heure_arrivee' => $request->heure_arrivee,
            'heure_depart' => $request->heure_depart,
            'session' => 1, // Vous pouvez adapter cela selon vos besoins
            'id_employe' => $request->id_employe,
            'id_statut' => $statut->id,
        ]);

        return response()->json([
            'message' => 'Permission enregistrée avec succès.',
            'data' => $pointage,
        ], 201);
    }

    public function findAll(Request $request) {
        $query = Pointage::with('statut')
            ->with('employe');
            // ->get();

            // Alternative: recherche par nom/prénom d'employé
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->whereHas('employe', function($q) use ($searchTerm) {
                $q->where('nom', 'ILIKE', "%{$searchTerm}%")
                ->orWhere('prenom', 'ILIKE', "%{$searchTerm}%")
                ->orWhere('cin', 'ILIKE', "%{$searchTerm}%")
                ->orWhere('telephone', 'ILIKE', "%{$searchTerm}%");
            });
        }

        $pointages = $query->orderBy('date', 'desc')
            ->get();
        return response()->json([
            'message'=>'Tous les pointages',
            'pointages' => $pointages
        ]);
    }

    public function ficheByEmploye($idEmploye) {
        $date = Carbon::now()->toDateString();

        $fiches = Pointage::where('id_employe', $idEmploye)
            ->where('date', '=', $date)
            ->with('statut')
            ->with('employe')
            ->get();

        return response()->json([
            'message' => 'Fiche de pointage d\'un employe',
            'fiche' => $fiches,
            'date' => $date
        ], 200);
    }
}
