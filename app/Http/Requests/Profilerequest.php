<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Profilerequest extends FormRequest
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
            'date_of_birth'=>'required|string',
            'gender'=>'required|string',
            'teaching_experience'=>'required|string',
            'teacher_type'=>'required|string',
            'teacher_qualification'=>'required|string',
            'teacher_title'=>'required|string',
            'date_of_employment'=>'required|string',
            'address'=>'required|string',
            'nationality'=>'required|string',
            'teacher_level'=>'required|string'
        ];
    }
}
