<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'faculty_id', 'description', 'duration'];
    public function timetables()
    {
        return $this->hasMany(TimeTable::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function getLevelsAttribute()
    {
        return range(100, $this->duration * 100, 100);
    }

    public function courseAssignments()
    {
        return $this->hasMany(CourseAssignment::class);
    }


    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_assignments')
            ->withPivot('semester_id', 'level')
            ->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_assignments')
            ->withPivot('academic_session_id', 'semester_id', 'course_id')
            ->withTimestamps();
    }
    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class);
    }


    public function semesters()
    {
        return $this->belongsToMany(Semester::class, 'department_semester')
            ->withPivot('max_credit_hours', 'level')
            ->withTimestamps();
    }

    // this builds a relationship between courses student registers for
    public function courseEnrollments()
    {
        return $this->hasManyThrough(CourseEnrollment::class, Student::class);
    }
}
