<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CreneauServiceManager;
use App\Models\CreneauService;

class CreneauServiceController extends Controller
{
    protected CreneauServiceManager $creneauServiceManager;
    public function __construct(CreneauServiceManager $creneauServiceManager)
    {
        $this->creneauServiceManager = $creneauServiceManager;
    }

    public function index() {
        return $this->creneauServiceManager->findAll();
    }

    public function findAllService($idService) {
        $creneaux = $this->creneauServiceManager->findAllService($idService);
        return response()->json([
            'creneaux' => $creneaux,
        ]);
    }

    public function store(Request $request) {
        $donnees = $request->validate([
            'jour' => 'required|int',
            'creneaux' => 'required|array',
            'id_service' => 'required|exists:service,id',
        ]);

        foreach ($donnees['creneaux'] as $creneau) {
            CreneauService::create([
                'jour' => $donnees['jour'],
                'heure' => $creneau,
                'id_service' => $donnees['id_service'],
            ]);
        }

        return response()->json(['message' => 'Créneaux ajoutés avec succès.'], 201);
    }

    public function destroy($idJour) {
        $this->creneauServiceManager->delete($idJour);
        return response()->json(['message' => 'Créneau supprimé avec succès.']);
    }
}