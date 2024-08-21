<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'teacher_assignments')
            ->withPivot('academic_session_id', 'semester_id', 'course_id')
            ->withTimestamps();
    }



    public function courses()
    {
        return $this->belongsToMany(Course::class, 'teacher_assignments')
            ->withPivot('department_id', 'academic_session_id', 'semester_id')
            ->withTimestamps();
    }
    public function scores()
    {
        return $this->hasMany(StudentScore::class);
    }
}
