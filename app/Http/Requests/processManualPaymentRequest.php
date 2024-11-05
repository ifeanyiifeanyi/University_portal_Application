<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class processManualPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invoice_id' => 'required|exists:invoices,id',
            'transaction_reference' => 'required|string',
            'bank_name' => 'required|string',
            'proof_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'additional_notes' => 'nullable|string',
            'metadata' => 'nullable|array'
        ];
    }
}
