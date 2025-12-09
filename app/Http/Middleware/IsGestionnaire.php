<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsGestionnaire
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role_id !== 4) {
            abort(403, 'Accès non autorisé');
        }
        return $next($request);
    }
}