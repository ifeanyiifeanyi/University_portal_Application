<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitPaymentFormRequest extends FormRequest
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
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'semester_id' => 'required|exists:semesters,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|numeric|min:100|max:600',
            'student_id' => 'required|exists:students,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array{
        return [
            'academic_session_id.required' => 'Associated academic session is required!',
            'semester_id.required' => 'Associated semester is required!',
            'payment_type_id.required' => 'Payment type is required!',
            'department_id.required' => 'Student department is required!',
            'level.required' => 'Student academic level is required!',
            'student_id.required' => 'Student is required!',
            'payment_method_id.required' => 'Payment type is required!',

        ];
    }
}
