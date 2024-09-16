<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AdminPaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::all();
        return view('admin.paymentMethod.index', compact('paymentMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.paymentMethod.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods',
            'description' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'config' => 'required|array',
            'config.payment_type' => 'required|string',
            'config.gateway' => 'required_if:config.payment_type,credit_card',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('payment_method_logos', 'public');
            $validated['logo'] = $logoPath;
        }

        // Remove any empty config values
        $validated['config'] = array_filter($request->input('config', []), function ($value) {
            return $value !== null && $value !== '';
        });

        PaymentMethod::create($validated);

        return redirect()->route('admin.payment_method.index')
            ->with('success', 'Payment method created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentMethod $paymentMethod)
    {
        return view('admin.paymentMethod.show', compact('paymentMethod'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.paymentMethod.edit', compact('paymentMethod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name,' . $paymentMethod->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'config' => 'required|array',
            'config.payment_type' => 'required|string',
            'config.gateway' => 'required_if:config.payment_type,credit_card',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($paymentMethod->logo) {
                Storage::disk('public')->delete($paymentMethod->logo);
            }
            $logoPath = $request->file('logo')->store('payment_method_logos', 'public');
            $validated['logo'] = $logoPath;
        }

        // Merge the existing config with the new config
        $existingConfig = $paymentMethod->config;
        $newConfig = $request->input('config', []);
        $mergedConfig = array_merge($existingConfig, $newConfig);

        // Remove any empty config values
        $validated['config'] = array_filter($mergedConfig, function ($value) {
            return $value !== null && $value !== '';
        });

        $paymentMethod->update($validated);

        return redirect()->route('admin.payment_method.index')
            ->with('success', 'Payment method updated successfully.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        // Delete logo if exists
        if ($paymentMethod->logo) {
            Storage::disk('public')->delete($paymentMethod->logo);
        }

        $paymentMethod->delete();

        return redirect()->route('admin.payment_method.index')
            ->with('success', 'Payment method deleted successfully.');
    }
}
