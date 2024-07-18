<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class createAcademicSessionRequest extends FormRequest
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
            'is_current' => 'sometimes|boolean',
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_current' => $this->has('is_current'),
        ]);
    }

}
