<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CreneauServiceManager;
use App\Models\CreneauService;
use Illuminate\Support\Facades\Log;

class CreneauServiceController extends Controller
{
    protected CreneauServiceManager $creneauServiceManager;
    public function __construct(CreneauServiceManager $creneauServiceManager)
    {
        $this->creneauServiceManager = $creneauServiceManager;
    }

    public function index()
    {
        return $this->creneauServiceManager->findAll();
    }

    public function findAllService($idService)
    {
        $creneaux = $this->creneauServiceManager->findAllService($idService);
        return response()->json([
            'creneaux' => $creneaux,
        ]);
    }

    public function findAllDirection($idDirection)
    {
        $creneaux = CreneauService::where('id_direction', $idDirection)->whereNull('id_service')->get();
        return response()->json([
            'creneaux' => $creneaux,
        ]);
    }

    // public function store(Request $request) {
    //     $donnees = $request->validate([
    //         'jour' => 'required|int',
    //         'creneaux' => 'required|array',
    //         'id_direction' => 'required|int|exists:direction,id',
    //         'id_service' => 'nullable|int|exists:service,id',
    //     ]);

    //     foreach ($donnees['creneaux'] as $creneau) {
    //         CreneauService::create([
    //             'jour' => $donnees['jour'],
    //             'heure' => $creneau,
    //             'id_direction' => $donnees['id_direction'],
    //             'id_service' => $donnees['id_service'],
    //         ]);
    //     }

    //     return response()->json(['message' => 'Créneaux ajoutés avec succès.'], 201);
    // }

    public function store(Request $request)
    {
        $donnees = $request->validate([
            'jour' => 'required|int',
            'creneaux' => 'required|array',
            'creneaux.*.heure' => 'required|string',
            'creneaux.*.heure_fin' => 'required|string',
            'id_direction' => 'required|int|exists:direction,id',
            'id_service' => 'nullable|int|exists:service,id',
        ]);

        foreach ($donnees['creneaux'] as $creneau) {
            CreneauService::create([
                'jour' => $donnees['jour'],
                'heure' => $creneau['heure'],
                'heure_fin' => $creneau['heure_fin'],
                'id_direction' => $donnees['id_direction'],
                'id_service' => $donnees['id_service'],
            ]);
        }

        return response()->json(['message' => 'Créneaux ajoutés avec succès.'], 201);
    }

    public function destroyByService($idService, $idJour, Request $request)
    {
        $donnees = $request->validate([
            'periodes' => 'required|array',
        ]);
        Log::info("idJour: " . $idJour . " idService: " . $idService);
        Log::info($donnees['periodes']);
        $this->creneauServiceManager->delete($idService, $idJour, $donnees['periodes']);
        return response()->json(['message' => 'Créneau supprimé avec succès.']);
    }

    public function destroyByDirection($idDirection, $idJour, Request $request)
    {
        $donnees = $request->validate([
            'periodes' => 'required|array',
        ]);
        Log::info("idJour: " . $idJour . " idDirection: " . $idDirection);
        Log::info($donnees['periodes']);
        $this->creneauServiceManager->deleteByDirection($idDirection, $idJour, $donnees['periodes']);
        return response()->json(['message' => 'Créneau supprimé avec succès.']);
    }
}
