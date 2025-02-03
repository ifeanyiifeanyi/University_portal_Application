<?php

namespace App\Http\Requests;

use App\Models\Invoice;
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
        $rules = [
            'invoice_id' => 'required|exists:invoices,id',
            'transaction_reference' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'proof_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'additional_notes' => 'nullable|string',
            'metadata' => 'nullable|array',
            'is_installment' => 'required|boolean',
        ];

        // Add conditional validation rules for installment payments
        if ($this->input('is_installment')) {
            $invoice = Invoice::find($this->input('invoice_id'));
            if ($invoice) {
                // $rules['base_amount'] = [
                //     'required',
                //     'numeric',
                //     'min:1',
                //     'lt:' . $invoice->amount // Must be less than total amount
                // ];
                $rules['next_installment_date'] = [
                    'required',
                    'date',
                    'after:today'
                ];
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'base_amount.lt' => 'The installment amount must be less than the total invoice amount.',
            'next_installment_date.after' => 'The next installment date must be a future date.'
        ];
    }
}
