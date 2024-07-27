<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'title', 'description', 'credit_hours'];

    public function courseAssignments()
    {
        return $this->hasMany(CourseAssignment::class);
    }


    public function departments()
    {
        return $this->belongsToMany(Department::class, 'course_assignments')
            ->withPivot('semester_id', 'level')
            ->withTimestamps();
    }


    public function semesters()
    {
        return $this->belongsToMany(Semester::class, 'course_assignments')
            ->withPivot('department_id', 'level')
            ->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_assignments')
            ->withPivot('department_id', 'academic_session_id', 'semester_id')
            ->withTimestamps();
    }
}
