<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    /**
    * Handle an unauthenticated user.
    */
    protected function unauthenticated($request, array $guards) {
        return response()->json(['message' => 'Token expiré ou non valide.'], 401);
    }
}
