<?php

namespace App\Http\Middleware;

use App\Support\TranslationManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale(TranslationManager::currentLocale());

        return $next($request);
    }
}
