<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AbleTo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!request()->user()->can($permission)) {
            // abort(403, 'You are unable to access');
            Session::flash('warning', 'You are unable to access');
            return redirect()->route('dashboard');
        }
        return $next($request);
    }
}
