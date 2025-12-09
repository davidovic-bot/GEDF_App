<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role_id !== 1) {
            abort(403, 'Accès non autorisé');
        }
        return $next($request);
    }
}