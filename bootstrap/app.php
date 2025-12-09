<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
        'issuperadmin'   => \App\Http\Middleware\IsSuperAdmin::class,
        'isadmin'         => \App\Http\Middleware\IsAdmin::class,
        'issecretaire'    => \App\Http\Middleware\IsSecretaire::class,
        'isgestionnaire'  => \App\Http\Middleware\IsGestionnaire::class,
        'ischefservice'   => \App\Http\Middleware\IsChefService::class,
        'isdirecteur'     => \App\Http\Middleware\IsDirecteur::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
