<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInvoiceStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $invoiceId = $request->route('invoice');
        $invoice = Invoice::findOrFail($invoiceId);

        if ($invoice->status == 'paid') {
            return redirect()->route('admin.payment.pay')
                ->with('info', 'This invoice has already been paid.');
        }
        return $next($request);
    }
}
