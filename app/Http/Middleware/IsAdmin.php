<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role_id !== 2) {
            abort(403, 'Accès non autorisé');
        }
        return $next($request);
    }
}