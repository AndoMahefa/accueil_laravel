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
    public function handle(Request $request, Closure $next, ...$roles)
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

        // Récupérer les rôles de l'employé via la relation
        $employeRoles = $employe->roles()->pluck('role')->toArray();
        Log::info('role(s) employe: ' . json_encode($employeRoles));
        Log::info('roles api.php: ' . json_encode($roles));
        // Vérifier si l'employé possède au moins un des rôles requis
        if (!array_intersect($roles, $employeRoles)) {
            return response()->json([
                'message' => 'Accès interdit',
                'required_roles' => $roles,
                'user_roles' => $employeRoles
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
