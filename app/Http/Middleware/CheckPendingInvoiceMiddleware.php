<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPendingInvoiceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $invoiceId = $request->route('invoiceId');
        $invoice = Invoice::find($invoiceId);

        if (!$invoice || $invoice->status !== 'pending') {
            return redirect()->route('admin.payment.pay')->with('error', 'Invalid or expired invoice.');
        }

        return $next($request);    }
}
