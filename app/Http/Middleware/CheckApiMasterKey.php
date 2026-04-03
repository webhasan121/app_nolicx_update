<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\ApiResponse;

class CheckApiMasterKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $providedKey = $request->header('X-MASTER-KEY');
        $masterKey = config('app.api_master_key');

        if (! $providedKey || $providedKey !== $masterKey) {
            return ApiResponse::unauthorized('Unauthorized - Invalid Master Key');
        }
        return $next($request);
    }
}
