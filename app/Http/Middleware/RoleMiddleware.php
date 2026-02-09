<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // ÉTAPE 1 : FORCE L'ACCÈS POUR TEST
        return $next($request);
        
        // ÉTAPE 2 : On verra après
    }
}