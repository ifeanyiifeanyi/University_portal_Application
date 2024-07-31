<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'season', 'start_date', 'end_date', 'is_current', 'academic_session_id'];

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    // public function courses()
    // {
    //     return $this->hasMany(Course::class);
    // }

    public function courseAssignments()
    {
        return $this->hasMany(CourseAssignment::class);
    }

    public function semesterRegistration()
    {
        return $this->hasMany(SemesterRegistration::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_assignments')
            ->withPivot('department_id', 'level')
            ->withTimestamps();
    }
}
