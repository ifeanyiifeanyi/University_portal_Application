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
            'marital_status' => 'nullable|in:Single,Married,Divorced,Widowed',
            'religion' => 'required|string',
            'bloodgroup' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'genotype' => 'nullable|in:AA,AS,AC,SS,SC,CC',
            'next_of_kin_name' => 'required|string',
            'next_of_kin_relationship' => 'required|string',
            'next_of_kin_phone' => 'required|string',
            'next_of_kin_address' => 'required|string',
            'jamb_registration_number' => 'nullable|string',
            'year_of_admission' => 'nullable|integer|min:1900|max:' . date('Y'),
            'mode_of_entry' => 'required|in:UTME,Direct Entry,Transfer',
            // 'current_level' => 'required|string',
        ];
    }

      /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'date_of_birth.before' => 'Date of birth must be a date before today.',
            'year_of_admission.min' => 'Year of admission must be a valid year.',
            'year_of_admission.max' => 'Year of admission cannot be in the future.',
            'department_id.exists' => 'Selected department does not exist.',
        ];
    }

     /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'firstname' => 'first name',
            'lastname' => 'last name',
            'othernames' => 'other names',
            'phonenumber' => 'phone number',
            'local_govt_of_origin' => 'local government of origin',
            'bloodgroup' => 'blood group',
            'next_of_kin_name' => 'next of kin name',
            'next_of_kin_relationship' => 'next of kin relationship',
            'next_of_kin_phone' => 'next of kin phone',
            'next_of_kin_address' => 'next of kin address',
            'jamb_registration_number' => 'JAMB registration number',
            'year_of_admission' => 'year of admission',
            'mode_of_entry' => 'mode of entry',
            // 'current_level' => 'current level',
        ];
    }
}
