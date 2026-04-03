<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsActiveVendor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isVendor() && !$request->user()?->isVendor?->where(['status' => 'Active'])) {
            // abort('403', 'Unable to access. Vendorship is not active');
            return redirect()->back()->with('info', "Unable to access. Vendorship is not active");
        }

        return $next($request);
    }
}
