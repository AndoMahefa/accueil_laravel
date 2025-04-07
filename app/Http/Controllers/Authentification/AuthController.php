<?php

namespace App\Http\Controllers\Authentification;

use App\Http\Controllers\Controller;
use App\Models\Direction;
use App\Models\Employe;
use App\Models\User;
use App\Services\FonctionnaliteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller {
    protected FonctionnaliteService $fonctionnaliteService;

    public function __construct(FonctionnaliteService $fonctionnaliteService) {
        $this->fonctionnaliteService = $fonctionnaliteService;
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'mot_de_passe' => 'required|string',
        ]);

        Log::info($request['mot_de_passe']);
        // Trouver l'utilisateur par email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request['mot_de_passe'], $user->mot_de_passe)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        // Authentification réussie, création d'un token
        $token = $user->createToken('token', ['*'], now()->addMinute(1440))->plainTextToken;

        // Récupérer les informations de l'utilisateur
        $role = $user->role; // Rôle principal : admin ou user

        // Si l'utilisateur est un employé, récupérer son service et ses rôles
        $employeInfo = null;
        $direction = null;
        $idService = 0;
        if ($user->id_employe && $user->role === 'user') {
            $employe = Employe::with(['service', 'direction'])->find($user->id_employe);

            if ($employe) {
                if($employe->service) {
                    $idService = $employe->service->id;
                }
                $direction = Direction::findOrFail($employe->id_direction);
                $utilisateur = $employe->utilisateur;
                $roles = $this->fonctionnaliteService->getItemsByUser($utilisateur->id);

                // Transformer les rôles pour renommer les clés
                $transformedRoles = $roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'title' => $role->titre, // Renommer "titre" en "title"
                        'to' => $role->vers,    // Renommer "vers" en "to"
                        'icon' => $role->icon,
                        'statut' => $role->statut,
                        'id_fonctionnalite' => $role->id_fonctionnalite,
                        'enfants' => $role->enfants->map(function($role_enfant) {
                            return [
                                'id' => $role_enfant->id,
                                'title' => $role_enfant->titre, // Renommer "titre" en "title"
                                'to' => $role_enfant->vers,    // Renommer "vers" en "to"
                                'icon' => $role_enfant->icon,
                                'statut' => $role_enfant->statut,
                                'id_fonctionnalite' => $role_enfant->id_fonctionnalite
                            ];
                        })
                    ];
                });

                $employeInfo = [
                    'nom' => $employe->nom,
                    'prenom' => $employe->prenom,
                    'service' => $employe->service->nom ?? 'Service inconnu',
                    'roles' => $transformedRoles,
                ];
            }
        } else if($user->id_employe && $user->role == 'admin') {
            $emp = Employe::with('service', 'direction')->find($user->id_employe);
            $direction = Direction::findOrFail($emp->id_direction);
            $idService = $emp->service->id;
            $employeInfo = [
                'nom' => $emp->nom,
                'prenom' => $emp->prenom,
                'service' => $emp->service->nom ?? 'Service inconnu',
            ];
        }

        // Construire la réponse
        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'idService' => $idService,
            'direction' => $direction,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $role,
                'employe_info' => $employeInfo,
            ],
        ], 200);
    }
}
