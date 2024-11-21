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
        $departmentId = $this->route('department') ? $this->route('department')->id : null;

        return [
            'name' => ['required', 'string', 'max:255', 'unique:departments,name,' . $departmentId],
            'faculty_id' => ['required', 'exists:faculties,id'],
            'duration' => ['required', 'integer', 'min:1', 'max:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'department_head_id' => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Department name must be unique.',
            'faculty_id.exists' => 'Selected faculty is invalid.',
            'program_id.exists' => 'Selected program is invalid.',
            'department_head_id.exists' => 'Selected department head is invalid.',
        ];
    }
}
