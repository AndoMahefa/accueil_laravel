<?php

namespace App\Http\Controllers;

use App\Services\VisiteurService;
use Illuminate\Http\Request;

class VisiteurController extends Controller {
    protected VisiteurService $visiteurService;

    public function __construct(VisiteurService $visiteurService) {
        $this->visiteurService = $visiteurService;
    }
    
    public function index() {
        $visiteurs = $this->visiteurService->findAll();

        return response()->json($visiteurs);
    }

    public function show($id) {
        $visiteur = $this->visiteurService->findById($id);

        return response()->json($visiteur);
    }

    public function store(Request $request) {
        $donnees_valides = $request->validate([
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:50',
            'cin' => 'required|string|max:20',
            'email' => 'required|string|max:50|email|unique:visiteur,email',
            'telephone' => 'required|string|max:50|regex:/^[0-9]+$/'
        ]);

        $visiteur = $this->visiteurService->create($donnees_valides);

        return response()->json($visiteur, 201);
    }

    public function update($id, Request $request) {
        $donnees_valides = $request->validate([
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:50',
            'cin' => 'required|string|max:20',
            'email' => 'required|string|max:50|email|unique:visiteur,email,'.$id,
            'telephone' => 'required|string|max:50|regex:/^[0-9]+$/'
        ]);

        $visiteur = $this->visiteurService->update($id, $donnees_valides);
        
        return response()->json($visiteur);
    }

    public function destroy($id){
        $visiteur = $this->visiteurService->delete($id);

        return response()->json(null, 204);
    }
}
