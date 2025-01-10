<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppelOffreService;
use Illuminate\Validation\ValidationException;

class AppelOffreController extends Controller
{
    protected $appelOffreService;
    public function __construct(AppelOffreService $appelOffreService) {
        $this->appelOffreService = $appelOffreService;
    }

    public function index(Request $request) {
        $status = $request->query('status');
        $status = $status ?? 0;

        return response()->json($this->appelOffreService->findAll($status));
    }

    public function show($id) {
        return response()->json($this->appelOffreService->findById($id));
    }

    public function store(Request $request)
    {
        try {
            $donnees = $request->validate([
                'titre' => 'required|string|max:255',
                'description' => 'required|string',
                'date_lancement' => 'required|date|after_or_equal:today',
                'date_limite' => 'required|date|after:date_lancement',
                'budget_estime' => 'required|numeric',
                // 'status' => 'required|int',
            ], [
                'titre.required' => 'Le champ titre est requis',
                'titre.max' => 'Le champ ne doit pas dépasser 255 caractères',
                'description.required' => 'Le champ description est requis',
                'date_lancement.required' => 'Le champ date de lancement est requis',
                'date_lancement.date' => 'La date de lancement doit être une date valide',
                'date_lancement.after_or_equal' => 'La date de lancement doit être supérieure ou égale à aujourd\'hui',
                'date_limite.required' => 'Le champ date limite est requis',
                'date_limite.date' => 'La date limite doit être une date valide',
                'date_limite.after' => 'La date limite doit être après la date de lancement',
                'budget_estime.required' => 'Le champ budget estimé est requis',
                'budget_estime.numeric' => 'Le budget estimé doit être un nombre',
                // 'status.required' => 'Le champ statut est requis',
                // 'status.int' => 'Le statut doit être un entier',
            ]);

            $donnees['status'] = 0;

            $appelOffre = $this->appelOffreService->create($donnees);
            return response()->json($appelOffre);
        } catch (ValidationException $erreur) {
            return response()->json($erreur->errors());
        }
    }

}
