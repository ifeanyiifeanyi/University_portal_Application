<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PaymentInstallmentConfig;
use App\Models\PaymentType;

class AdminInstallmentConfigController extends Controller
{
    public function index()
    {
        $configs = PaymentInstallmentConfig::with('paymentType')->latest()->get();
        $paymentTypes = PaymentType::whereDoesntHave('installmentConfig')->get();

        return view('admin.installments.config.index', compact('configs', 'paymentTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_type_id' => 'required|exists:payment_types,id',
            'number_of_installments' => 'required|integer|min:2',
            'minimum_first_payment_percentage' => 'required|numeric|min:1|max:99',
            'interval_days' => 'required|integer|min:1',
            'late_fee_amount' => 'required|numeric|min:0',
            'late_fee_type' => 'required|in:fixed,percentage'
        ]);

        PaymentInstallmentConfig::create($validated);

        return redirect()->route('admin.installment-config.index')
            ->with('success', 'Installment configuration created successfully');
    }

    public function edit(PaymentInstallmentConfig $config)
    {
        return response()->json($config);
    }

    public function update(Request $request, PaymentInstallmentConfig $config)
    {
        $validated = $request->validate([
            'number_of_installments' => 'required|integer|min:2',
            'minimum_first_payment_percentage' => 'required|numeric|min:1|max:99',
            'interval_days' => 'required|integer|min:1',
            'late_fee_amount' => 'required|numeric|min:0',
            'late_fee_type' => 'required|in:fixed,percentage',
            'is_active' => 'boolean'
        ]);

        $config->update($validated);

        return redirect()->route('admin.installment-config.index')
            ->with('success', 'Installment configuration updated successfully');
    }

    public function destroy(PaymentInstallmentConfig $config)
    {
        $config->delete();

        return redirect()->route('admin.installment-config.index')
            ->with('success', 'Installment configuration deleted successfully');
    }
}
