<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    // public function handle(Request $request, Closure $next, ...$services)
    public function handle(Request $request, Closure $next, ...$directions)
    {
        $user = Auth::guard('sanctum')->user();
        Log::info('Token reçu:', [
            'token' => $request->bearerToken(),
            'user' => $user ? 'User trouvé' : 'User non trouvé'
        ]);
        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $employe = $user->employe;
        if (!$employe) {
            return response()->json(['message' => 'Aucun employé associé'], 403);
        }

        $direction = $employe->direction;
        if(!$direction) {
            return response()->json(['message' => 'Aucun direction associée'], 403);
        }

        if(in_array($direction->nom, $directions)) {
            return $next($request);
        }

        // $service = $employe->service;
        // if (!$service) {
        //     return response()->json(['message' => 'Aucun service associé'], 403);
        // }

        // // Vérifie si le service de l'employé est dans la liste des services autorisés
        // if (in_array($service->nom, $services)) {
        //     return $next($request);
        // }

        // return response()->json([
        //     'message' => 'Service non autorisé',
        //     'required_services' => $services,
        //     'user_service' => $service->nom
        // ], 403);
        return response()->json([
            'message' => 'Direction non autorisé',
            'required_direction' => $directions,
            'user_direction' => $direction->nom
        ], 403);
    }
}

?>
