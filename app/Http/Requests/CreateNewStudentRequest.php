<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewStudentRequest extends FormRequest
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
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'department_id' => 'required|exists:departments,id',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'state_of_origin' => 'required|string|max:255',
            'lga_of_origin' => 'required|string|max:255',
            'hometown' => 'required|string|max:255',
            'residential_address' => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'marital_status' => 'required|string|max:255',
            'religion' => 'required|string|max:255',
            'blood_group' => 'required|string|max:255',
            'genotype' => 'required|string|max:255',
            'next_of_kin_name' => 'required|string|max:255',
            'next_of_kin_relationship' => 'required|string|max:255',
            'next_of_kin_phone' => 'required|string|max:20',
            'next_of_kin_address' => 'required|string|max:255',
            'jamb_registration_number' => 'nullable|string|max:255',
            'year_of_admission' => 'required|digits:4',
            'mode_of_entry' => 'required|in:UTME,Direct Entry,Transfer',
            'current_level' => 'required|string|max:255',
            'profile_photo' => 'nullable|image|max:2048',
        ];
    }
}
