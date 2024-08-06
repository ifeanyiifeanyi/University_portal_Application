<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// THIS IS USED TO ASSIGN COURSES TO DEPARTMENT, SEMESTER, AND ACADEMIC SESSION
class CourseAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'department_id', 'semester_id', 'level'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class, 'course_id', 'course_id')
            ->where('semester_id', $this->semester_id)
            ->where('department_id', $this->department_id);
    }



    public function teacherAssignment()
    {
        return $this->hasOne(TeacherAssignment::class, 'course_id', 'course_id')
            ->where('semester_id', $this->semester_id)
            ->where('department_id', $this->department_id);
    }


    public function assignedTeacher()
    {
        return $this->hasOneThrough(
            Teacher::class,
            TeacherAssignment::class,
            'course_id', // Foreign key on TeacherAssignment table
            'id', // Foreign key on Teacher table
            'course_id', // Local key on CourseAssignment table
            'teacher_id' // Local key on TeacherAssignment table
        )->where('teacher_assignments.semester_id', $this->semester_id)
            ->where('teacher_assignments.department_id', $this->department_id);
    }
}
