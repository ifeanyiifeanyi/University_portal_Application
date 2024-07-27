<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'faculty_id' => ['required', 'integer'],
            'duration' => ['required', 'integer', 'max:8', 'min:1'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The department name field is required.',
            'duration.required' => 'This must be a whole positive single digit.',
            'faculty_id.required' => 'The faculty field is required.',
            'description.string' => 'The description field should be a string.',
            'description.max' => 'The description field should not exceed 255 characters.',
        ];
    }
}
