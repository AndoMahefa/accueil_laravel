<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RendezVousService;
use App\Models\RendezVous;

class RendezVousController extends Controller
{
    protected RendezVousService $rendezVousService;
    public function __construct(RendezVousService $rendezVousService)
    {
        $this->rendezVousService = $rendezVousService;
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
}
