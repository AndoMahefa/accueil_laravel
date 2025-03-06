<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Récupérer l'utilisateur actuellement authentifié
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Non autorisé'], Response::HTTP_UNAUTHORIZED);
        }

        // Si l'utilisateur est un admin, il a un accès illimité
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Vérifier si l'utilisateur est associé à un employé
        $employe = $user->employe;
        if (!$employe) {
            return response()->json(['message' => 'Aucun employé associé à cet utilisateur'], Response::HTTP_FORBIDDEN);
        }

        if ($role !== $user->role) {
            return response()->json([
                'message' => 'Accès interdit',
                'required_roles' => $role,
                'role_user' => $user->role
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
