<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands()
    ->withMiddleware(function (Middleware $middleware): void {
        // The host says which language the page is written in, so nothing may
        // render — or be read back out of the per-domain static cache — before
        // this has run.
        $middleware->web(prepend: [
            \App\Http\Middleware\SetLocaleFromHost::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            // Every page here is server-rendered HTML, so it is worth
            // shipping without the indentation. The feed and the sitemap
            // declare their own content type and are skipped.
            \Backstage\MinifyHtml\Middleware\MinifyHtml::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'signed' => \App\Http\Middleware\ValidateSignature::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
