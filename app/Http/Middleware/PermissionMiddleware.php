<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('login')->with('error', 'You must be logged in to access this page.');
        }

        if (!$request->user()->can($permission)) {
            // You can customize this based on your application's needs
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
