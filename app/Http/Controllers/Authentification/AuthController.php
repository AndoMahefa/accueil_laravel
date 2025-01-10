<?php

namespace App\Http\Controllers\Authentification;

use App\Http\Controllers\Controller;
use App\Models\Employe;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller {
    // public function login(Request $request) {
    //     // Validation des informations d'identification
    //     $credentials = $request->validate([
    //         'email' => 'required|string|email',
    //         'mot_de_passe' => 'required|string'
    //     ]);

    //     // Recherche de l'utilisateur par email
    //     $service = Service::where('email', $credentials['email'])->first();

    //     // Vérification du mot de passe
    //     if (!$service || !Hash::check($credentials['mot_de_passe'], $service->mot_de_passe)) {
    //         return response()->json(['message' => 'Identifiants incorrects'], 401);
    //     }

    //     // Génération d'un token personnalisé basé sur le nom du service
    //     // $tokenName = 'token-' . $service->nom;
    //     // $token = $service->createToken($tokenName, ['*'], now()->addMinute(1440))->plainTextToken;
    //     $token = $service->createToken("token", ['*'], now()->addMinute(1440))->plainTextToken;

    //     return response()->json([
    //         'message' => 'Connexion réussie',
    //         'token' => $token,
    //         'token_type' => 'Bearer',
    //         'role' => $service->nom,
    //         'idService' => $service->id
    //     ], 200);
    // }

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
        if ($user->id_employe && $user->role === 'user') {
            $employe = Employe::with(['service', 'roles'])->find($user->id_employe);

            if ($employe) {
                Log::info('Roles recuperes : ', ['roles' => $employe->roles]);
                $rolesEmploye = $employe->roles->pluck('role')->all();
                // foreach($employe->roles as $role) {
                //     $rolesEmploye[] = $role->role;
                // }
                Log::info('Relations chargées:', [
                    'roles' => $employe->roles->toArray(),
                ]);
                Log::info('Rôles extraits:', [
                    'roles' => $rolesEmploye
                ]);
                $employeInfo = [
                    'nom' => $employe->nom,
                    'prenom' => $employe->prenom,
                    'service' => $employe->service->nom ?? 'Service inconnu',
                    'roles' => $rolesEmploye,
                ];
            }
        } else if($user->id_employe && $user->role == 'admin') {
            $emp = Employe::with('service')->find($user->id_employe);
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
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $role,
                'employe_info' => $employeInfo,
            ],
        ], 200);
    }
}
