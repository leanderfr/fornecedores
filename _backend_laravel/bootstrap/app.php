<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Cors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    //***********************************************************************************
    // permite acesso do front end que esta em outra aplicacao para validacao de logins
    //***********************************************************************************
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: ["*"]);
        $middleware->append(Cors::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
