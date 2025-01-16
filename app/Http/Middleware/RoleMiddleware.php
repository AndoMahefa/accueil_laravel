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
        $user = Auth::guard('sanctum')->user(); // Récupère l'utilisateur authentifié en tant que service

        Log::info('utilisateur: ' . $user);
        // Vérifiez si le service a un rôle parmi ceux autorisés
        $employe = $user->employe;
        $service = null;
        if($employe) {
            $service = $employe->service;
        }

        Log::info('employe: ' . $employe->nom);
        if ($service) {
            Log::info('Service actuel : ' . $service->nom);
            if ($service && in_array($service->nom, explode('|', implode(',', $roles)))) {
                return $next($request);
            }
        }

        Log::warning('Accès refusé : ' . ($service->nom ?? 'Aucun'));
        return response()->json(["message" => "Aucun service associé à cet utilisateur"], 403);
    }
}

?>
