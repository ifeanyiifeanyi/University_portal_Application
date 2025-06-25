<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'title',
        'description',
        'credit_hours',
        'course_type',
        'program_id',
        'created_by',
        'updated_by'
    ];

    public function admin()
    {
        // return $this->belongsToMany(User::class,'created_by');
        return $this->belongsTo(Admin::class, 'created_by', 'user_id');

    }

    public function timetables()
    {
        return $this->hasMany(TimeTable::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

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

    public function students()
    {
        return $this->belongToMany(CourseEnrollment::class);
    }
    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    public function scores()
    {
        return $this->hasMany(StudentScore::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    // public function results()
    // {
    //     return $this->hasMany(Result::class);
    // }
    public function gpaRecords()
    {
        return $this->hasMany(GpaRecord::class);
    }
}
