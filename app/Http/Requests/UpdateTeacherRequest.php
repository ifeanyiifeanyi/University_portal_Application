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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'other_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:users,email,' . $this->teacher->user->id,
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'teaching_experience' => 'nullable|string|max:255',
            'teacher_type' => 'nullable|string|in:Full-time,Part-time,Auxiliary',
            'teacher_qualification' => 'nullable|string|max:255',
            'teacher_title' => 'nullable|string|max:255',
            'office_hours' => 'nullable|string|max:255',
            'office_address' => 'nullable|string|max:255',
            'biography' => 'nullable|string',
            'certifications' => 'nullable|string',
            'publications' => 'nullable|string',
            'number_of_awards' => 'nullable|integer|min:0',
            'date_of_employment' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'level' => 'nullable|string|in:Senior Lecturer,Junior Lecturer,Technician',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
