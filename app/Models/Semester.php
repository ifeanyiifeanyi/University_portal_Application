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

    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class);
    }


    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_assignments')
            ->withPivot('department_id', 'level')
            ->withTimestamps();
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_semester')
            ->withPivot('max_credit_hours')
            ->withTimestamps();
    }

    public function canBeDeleted()
    {
        return !$this->is_current && !$this->courseAssignments()->exists() && !$this->teacherAssignments()->exists();
    }


    protected $casts = [
        // 'start_date' => 'date',
        // 'end_date' => 'date',
        'is_current' => 'boolean',
    ];
}
