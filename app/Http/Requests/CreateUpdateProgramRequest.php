<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateUpdateProgramRequest extends FormRequest
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
        $programId = $this->route('program') ? $this->route('program')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('programs', 'name')->ignore($programId)
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('programs', 'code')->ignore($programId)
            ],
            'description' => 'nullable|string|max:1000',
            'class_schedule_type' => [
                'required',
                Rule::in(['morning', 'evening', 'weekend', 'flexible', 'hybrid'])
            ],
            'duration_type' => [
                'required',
                Rule::in(['years', 'semesters', 'months'])
            ],
            'duration_value' => 'required|integer|min:1|max:10',
            'attendance_requirement' => 'required|numeric|between:0,100',
            'tuition_fee_multiplier' => 'required|numeric|between:0.01,10.00',
            'status' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A program with this name already exists.',
            'code.unique' => 'A program with this code already exists.',
            'duration_value.between' => 'Duration value must be between 1 and 10.',
            'attendance_requirement.between' => 'Attendance requirement must be between 0 and 100.',
            'tuition_fee_multiplier.between' => 'Tuition fee multiplier must be between 0.01 and 10.00',
        ];
    }
}
