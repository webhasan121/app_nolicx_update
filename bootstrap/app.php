<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckApiMasterKey;
use App\Http\Middleware\CheckApiRequestIsGet;
use App\Http\Middleware\CheckApiRequestIsPost;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\TrackProductViewForReseller;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(
            [
                'auth.master' => CheckApiMasterKey::class,
                'products.view.add' => TrackProductViewForReseller::class,
            ]
        );
        $middleware->web(append: [
            SetLocale::class,
            HandleInertiaRequests::class,
        ]);
        // $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
