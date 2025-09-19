<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use App\Http\Middleware\SanctumCookieToken;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
   ->withMiddleware(function ($middleware) {
        // Assign aliases
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,

        ]);

        // Assign global middleware
                $middleware->prepend(HandleCors::class);

        $middleware->prepend(SanctumCookieToken::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
