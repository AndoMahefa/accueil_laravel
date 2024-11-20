<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTokenExpiration {
    public function handle(Request $request, Closure $next) {
        if (!$request->user()) {
            return response()->json(['message' => 'Token expiré ou invalide.'], 401);
        }
    
        return $next($request);
    }
}