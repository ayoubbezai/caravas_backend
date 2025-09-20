<?php

use App\Http\Middleware\HandleCors;
use Illuminate\Foundation\Application;
use App\Http\Middleware\SanctumCookieToken;
use Illuminate\Foundation\Configuration\Exceptions;
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

        ]);

        // Assign global middleware
                $middleware->prepend(HandleCors::class);

        $middleware->prepend(SanctumCookieToken::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
