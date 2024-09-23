<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyReceiptAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $receipt = $request->route('receipt');

        if (!$receipt) {
            abort(404);
        }

        // Check if the user is authorized to view this receipt
        if (auth()->check() && (auth()->user()->id === $receipt->payment->student->user_id || auth()->user()->isAdmin())) {
            return $next($request);
        }

        abort(403, 'Unauthorized access');    }
}
