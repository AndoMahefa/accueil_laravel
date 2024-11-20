<?php

namespace App\Http\Controllers\Authentification;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
    public function login(Request $request) {
        // Validation des informations d'identification
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'mot_de_passe' => 'required|string'
        ]);

        // Recherche de l'utilisateur par email
        $service = Service::where('email', $credentials['email'])->first();

        // Vérification du mot de passe 
        if (!$service || !Hash::check($credentials['mot_de_passe'], $service->mot_de_passe)) {
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }

        // Génération d'un token personnalisé basé sur le nom du service
        // $tokenName = 'token-' . $service->nom;
        // $token = $service->createToken($tokenName, ['*'], now()->addMinute(1440))->plainTextToken;
        $token = $service->createToken("token", ['*'], now()->addMinute(1440))->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'token_type' => 'Bearer',
            'role' => $service->nom,
            'idService' => $service->id
        ], 200);
    }
}
