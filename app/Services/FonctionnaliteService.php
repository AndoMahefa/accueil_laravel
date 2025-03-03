<?php

namespace App\Services;

use App\Models\Fonctionnalite;
use App\Models\RoleUtilisateur;
use Illuminate\Support\Facades\Log;

class FonctionnaliteService {
    public function getItemsByUser($idUser) {
        // Récupérer les IDs des fonctionnalités associées à l'utilisateur via la table role_utilisateur
        $fonctionnaliteIds = RoleUtilisateur::where('id_utilisateur', $idUser)
            ->pluck('id_fonctionnalite');

        Log::info($fonctionnaliteIds);

        // Récupérer les fonctionnalités principales (sans parent) qui sont associées à l'utilisateur
        $fonctionnalites = Fonctionnalite::whereNull('id_fonctionnalite')
            ->whereIn('id', $fonctionnaliteIds)
            ->with('enfants')
            ->get();

        Log::info($fonctionnalites);

        return $fonctionnalites;
    }
}
