<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
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
    public function rules()
    {
        return [
            // User fields
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'other_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'required|email|max:255|unique:users,email,' . $this->route('teacher')->user_id,
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // Teacher fields
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string',
            'teaching_experience' => 'nullable|string',
            'teacher_type' => 'nullable|string',
            'teacher_qualification' => 'nullable|string',
            'teacher_title' => 'nullable|string',
            'office_hours' => 'nullable|string',
            'office_address' => 'nullable|string',
            'biography' => 'nullable|string',
            'certifications' => 'nullable|array',
            'certifications.*' => 'nullable|string',
            'publications' => 'nullable|array',
            'publications.*' => 'nullable|string',
            'number_of_awards' => 'nullable|numeric',
            'date_of_employment' => 'nullable|date',
            'address' => 'nullable|string',
            'nationality' => 'nullable|string',
            'level' => 'nullable|string',
        ];

    }
}
