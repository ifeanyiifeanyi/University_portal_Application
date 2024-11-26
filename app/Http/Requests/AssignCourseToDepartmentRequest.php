<?php

namespace App\Http\Requests;

use App\Models\CourseAssignment;
use Illuminate\Foundation\Http\FormRequest;

class AssignCourseToDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if the user is an admin and has the role of superAdmin
        return request()->user()->user_type == 1 &&
            optional(request()->user()->admin)->role === 'superAdmin';
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_id' => 'required|exists:courses,id',
            'department_id' => 'required|exists:departments,id',
            'semester_id' => 'required|exists:semesters,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'level' => 'required|integer|min:100|max:600|multiple_of:100',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'course_id' => $this->course_id,
            'department_id' => $this->department_id,
            'semester_id' => $this->semester_id,
            'academic_session_id' => $this->academic_session_id,
            'level' => $this->level,
        ]);
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $exists = CourseAssignment::where([
                'course_id' => $this->course_id,
                'department_id' => $this->department_id,
                'semester_id' => $this->semester_id,
                'academic_session_id' => $this->academic_session_id,
                'level' => $this->level,
            ])->exists();

            if ($exists) {
                $validator->errors()->add('course_id', 'This course is already assigned to the specified department, level, semester, and academic session.');
            }
        });
    }
}
