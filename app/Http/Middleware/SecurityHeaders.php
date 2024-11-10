<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
         // Security headers
         $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
         $response->headers->set('X-XSS-Protection', '1; mode=block');
         $response->headers->set('X-Content-Type-Options', 'nosniff');
         $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

         // Optional: Add Content Security Policy
         $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';");

         // Optional: Add Permissions Policy
         $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

         // Optional: Add Strict Transport Security
         if (config('app.env') === 'production') {
             $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
         }

         return $response;
    }
}
