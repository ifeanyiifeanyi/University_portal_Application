<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentprofileRequest extends FormRequest
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
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'othernames' => 'required|string',
            'phonenumber' => 'required|string|min:10|max:15',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'state_of_origin' => 'required|string',
            'local_govt_of_origin' => 'required|string',
            'hometown' => 'required|string',
            'residential_address' => 'required|string',
            'permanent_address' => 'required|string',
            'nationality' => 'required|string',
            'marital_status' => 'required|string',
            'religion' => 'required|string',
            'bloodgroup' => 'required|string',
            'genotype' => 'required|string',
            'next_of_kin_name' => 'required|string',
            'next_of_kin_relationship' => 'required|string',
            'next_of_kin_phone' => 'required|string',
            'next_of_kin_address' => 'required|string',
            'jamb_registration_number' => 'nullable|string',
            'year_of_admission' => 'required|integer',
            'mode_of_entry' => 'required|in:UTME,Direct Entry,Transfer',
            // 'current_level' => 'required|string',
            'profile_photo' => 'nullable|image|max:2048',
        ];
    }
}
