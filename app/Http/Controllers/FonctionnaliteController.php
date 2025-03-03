<?php

namespace App\Http\Controllers;

use App\Models\Fonctionnalite;
use App\Models\RoleUtilisateur;
use Illuminate\Http\Request;

class FonctionnaliteController extends Controller {
    public function items() {
        // Récupérer uniquement les fonctionnalités principales (sans parent)
        $fonctionnalites = Fonctionnalite::whereNull('id_fonctionnalite')
            ->with('enfants')
            ->get();

        return response()->json($fonctionnalites);
    }

    public function itemsByUser($idUser) {
        // Récupérer uniquement les fonctionnalités principales (sans parent)
        $fonctionnalites = Fonctionnalite::whereNull('id_fonctionnalite')
            ->where('id_utilisateur', $idUser)
            ->with('enfants')
            ->get();

        return response()->json($fonctionnalites);
    }

    public function fonctionnalites() {
        $fonctionnalites = Fonctionnalite::all();

        return response()->json($fonctionnalites);
    }

    public function assignRoleUser(Request $request) {
        $donnees = $request->validate([
            'id_utilisateur' => 'required|int|exists:utilisateur,id',
            'id_fonctionnalite' => 'required|array',
            'id_fonctionnalite.*' => 'required|int|exists:fonctionnalite,id'
        ]);

        $rolesAssignes = [];

        foreach ($donnees['id_fonctionnalite'] as $idFonctionnalite) {
            $roleData = [
                'id_utilisateur' => $donnees['id_utilisateur'],
                'id_fonctionnalite' => $idFonctionnalite
            ];

            // Vérifier si le rôle existe déjà pour éviter les doublons
            $roleExistant = RoleUtilisateur::where('id_utilisateur', $roleData['id_utilisateur'])
                ->where('id_fonctionnalite', $roleData['id_fonctionnalite'])
                ->first();

            if (!$roleExistant) {
                $roleAssigne = RoleUtilisateur::create($roleData);
                $rolesAssignes[] = $roleAssigne;
            }
        }

        return response()->json([
            'message' => count($rolesAssignes) > 0
                ? 'Rôles assignés avec succès'
                : 'Aucun nouveau rôle assigné',
            'roles' => $rolesAssignes,
            'total_assigne' => count($rolesAssignes)
        ]);
    }

    public function findRoleByUser($idUser) {
        // Récupère les rôles de l'utilisateur
        $roles = RoleUtilisateur::where('id_utilisateur', $idUser)
            ->with('fonctionnalite')
            ->orderBy('id_fonctionnalite')
            ->get();

        return response()->json([
            'message' => 'Rôles de l\'utilisateur',
            'roles' => $roles->values() // Réindexe les clés du tableau
        ]);
    }


    // public function findRoleByUser($idUser) {
    //     // Récupère les rôles de l'utilisateur
    //     $roles = RoleUtilisateur::where('id_utilisateur', $idUser)
    //         ->with(['fonctionnalite' => function($query) {
    //             // Exclut les fonctionnalités parentes qui ont des enfants
    //             $query->whereDoesntHave('enfants');
    //         }])
    //         ->get()
    //         ->filter(function($role) {
    //             // Exclut les rôles où la fonctionnalité est null ou a des enfants
    //             return $role->fonctionnalite !== null;
    //         });

    //     return response()->json([
    //         'message' => 'Rôles de l\'utilisateur (hors parents avec enfants)',
    //         'roles' => $roles->values() // Réindexe les clés du tableau
    //     ]);
    // }
}
