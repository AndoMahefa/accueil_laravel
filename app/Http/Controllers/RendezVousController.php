<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RendezVousService;
use App\Models\RendezVous;
use App\Services\CreneauServiceManager;

class RendezVousController extends Controller {
    protected RendezVousService $rendezVousService;
    protected CreneauServiceManager $creneauService;
    
    public function __construct(RendezVousService $rendezVousService, CreneauServiceManager $creneauService) {
        $this->rendezVousService = $rendezVousService;
        $this->creneauService = $creneauService;
    }

    public function index() {
        return $this->rendezVousService->findAll();
    }

    public function store(Request $request) {
        $donnees = $request->validate([
            'date_heure' => 'required|date|after:now',
            'id_service' => 'required|exists:service,id',
            'id_visiteur' => 'required|exists:visiteur,id',
            'motif' => 'required|string'
        ]);

        $rdv = $this->rendezVousService->create($donnees);
        return response()->json([
            'message' => 'Rendez-vous créé avec succès',
            'rdv' => $rdv,
        ]);
    }

    public function findHeureIndispo(Request $request) {
        $validated = $request->validate([
            'date' => 'required|date',
            'id_service' => 'required|integer|exists:service,id',
        ]);

        $indisponibilites = RendezVous::whereDate('date_heure', $validated['date'])
            ->where('id_service', $validated['id_service'])
            ->pluck('date_heure')
            ->map(function ($dateTime) {
                return date('H:i', strtotime($dateTime));
            });

        return response()->json($indisponibilites);
    }

    public function jourDispoService($idService) {
        $jourDispo = $this->creneauService->jourDispoService($idService);
        return response()->json(['joursDispo'=> $jourDispo]);
    }

    public function findCreneauxServiceJour($idService, $day) {
        $creneaux = $this->creneauService->getCreneauxServiceJour($idService, $day);
        return response()->json(['creneaux' => $creneaux]);
    }

    public function findRdvByService($idService, Request $request) {
        $date = $request->query('date');
        $date = $date ?? now()->toDateString();

        $rdvs = $this->rendezVousService->findRdvByService($idService, $date);

        return response()->json(["rdv" => $rdvs]);
    }
}
