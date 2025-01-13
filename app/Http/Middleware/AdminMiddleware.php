<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     if (!empty(Auth::check()) && Auth::user()->user_type == 1) {
    //         return $next($request);
    //     }else{
    //         Auth::logout();
    //         return to_route('login.view');
    //     }
    // }




    public function handle(Request $request, Closure $next): Response
    {
        // Only allow actual admins, not impersonated ones
        if (!empty(Auth::check()) && Auth::user()->user_type == 1) {
            return $next($request);
        }

        Auth::logout();
        return to_route('login.view');
    }
}
