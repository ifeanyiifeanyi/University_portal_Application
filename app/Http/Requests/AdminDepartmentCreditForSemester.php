<?php

namespace App\Http\Requests;

use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;

class AdminDepartmentCreditForSemester extends FormRequest
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
            'department_id' => 'required|exists:departments,id',
            'semester_id'  => 'required|exists:semesters,id',
            'max_credit_hours' => 'required|numeric|min:1',
            'level' => 'required|multiple_of:100'
        ];
    }

    public function messages(): array{
        return [
            'department_id.required' => 'Department is required',
            'department_id.exists' => 'Department does not exist',
            'semester_id.required' => 'Semester is required',
            'level.required' => 'Department Level is required'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $department = Department::findOrFail($this->department_id);
            $maxLevel = $department->duration * 100;

            if ($this->level > $maxLevel) {
                $validator->errors()->add('level', 'Level must be less than or equal to ' . $maxLevel);
            }

            $existingAssignment = DB::table('department_semester')
                ->where('department_id', $this->department_id)
                ->where('level', $this->level)
                ->first();

            if ($existingAssignment) {
                $validator->errors()->add('level', 'Credit load for this level already exists.');
            }
        });
    }
}
