<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\ProveOfPayment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\processManualPaymentRequest;
use App\Services\ManualPaymentService;

class ProofOfPaymentController extends Controller
{

    protected $manualPaymentService;
    public function __construct(ManualPaymentService $manualPaymentService)
    {
        $this->manualPaymentService = $manualPaymentService;
    }
    public function processManualPayment(processManualPaymentRequest $request)
    {
        $validated = $request->validated();

        try {
            $invoice = Invoice::findOrFail($validated['invoice_id']);
            $payment = $this->manualPaymentService->processManualPayment(
                array_merge($validated, ['invoice' => $invoice]),
                $request->file('proof_file')
            );

            return redirect()
                ->route('admin.payments.showConfirmation_prove', $payment->id)
                ->with('success', 'Payment proof submitted successfully. Awaiting verification.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showConfirmationProve($paymentId){
        $payment = Payment::with('invoice')->findOrFail($paymentId);

        return view('admin.payments.proof_of_payment', compact('payment'));

    }
}
