<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLecturerRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'other_name' => 'nullable|string|max:255',
            'phone' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string',
            'teaching_experience' => 'required|integer',
            'teacher_type' => 'required|string',
            'teacher_qualification' => 'required|string|max:255',
            'teacher_title' => 'required|string|max:255',
            'office_hours' => 'nullable|string|max:255',
            'office_address' => 'nullable|string|max:255',
            'biography' => 'nullable|string',
            'certifications' => 'nullable|array',
            'publications' => 'nullable|array',
            'number_of_awards' => 'nullable|integer',
            'date_of_employment' => 'required|date',
            'address' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'level' => 'required|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];
    }
}
