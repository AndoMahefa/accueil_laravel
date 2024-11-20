<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $service = Auth::guard('sanctum')->user(); // Récupère l'utilisateur authentifié en tant que service

        // Vérifiez si le service a un rôle parmi ceux autorisés
        if ($service) {
            Log::info('Rôle actuel : ' . $service->nom);
            if ($service && in_array($service->nom, explode('|', implode(',', $roles)))) {
                return $next($request);
            }            
        }
        Log::warning('Accès refusé pour le rôle : ' . ($service->nom ?? 'Aucun'));
        return response()->json(['message' => 'Accès non autorisé'], 403);
        
    }
}

?>