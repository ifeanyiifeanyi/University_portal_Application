<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Models\ProveOfPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\ManualPaymentService;
use App\Http\Requests\processManualPaymentRequest;

class ProofOfPaymentController extends Controller
{

    protected $manualPaymentService;
    public function __construct(ManualPaymentService $manualPaymentService)
    {
        $this->manualPaymentService = $manualPaymentService;
    }



    public function processManualPayment(ProcessManualPaymentRequest $request)
    {
        $validated = $request->validated();

        try {
            $invoice = Invoice::findOrFail($validated['invoice_id']);

            // Prepare payment data
            $paymentData = [
                'invoice' => $invoice,
                'transaction_reference' => $request->transaction_reference,
                'bank_name' => $request->bank_name,
                'additional_notes' => $request->additional_notes,
                'metadata' => $request->metadata,
                'is_installment' => (bool)$request->is_installment,
            ];

            // Add installment-specific data if applicable
            if ($paymentData['is_installment']) {
                $paymentData['base_amount'] = $request->base_amount;
                $paymentData['next_installment_date'] = $request->next_installment_date;
            }

            $paymentTypeAmount = $invoice->paymentType->amount;
            $result = $this->manualPaymentService->processManualPayment(
                $paymentData,
                $request->file('proof_file'),
                $paymentTypeAmount
            );

            return redirect()
                ->route('admin.payments.showConfirmation_prove', $result['payment']->id)
                ->with('success', 'Payment proof submitted successfully. Awaiting verification.');
        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }


    public function showConfirmationProve($paymentId)
    {
        $payment = Payment::with('invoice')->findOrFail($paymentId);

        return view('admin.payments.proof_of_payment', compact('payment'));
    }

    public function destroy($invoice)
    {

        $invoice =  Invoice::findOrFail($invoice)->first();
        //    dd($invoice);
        return redirect()->back()->with('success', 'Invoice delete successfully!');
    }
}
