<?php

namespace App\Http\Middleware;

use App\Models\vendor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Owner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, vendor $Ru): Response
    {
        /**
         * if request is owner user
         */
        if ($Ru->user_id != Auth::id()) {
            return $next($request);
        } else {
            return redirect()->route('user.dash')->with('warning', 'You are not the owner !');
        }
    }
}
