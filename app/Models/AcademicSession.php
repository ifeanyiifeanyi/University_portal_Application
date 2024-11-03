<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_date', 'end_date', 'is_current'];
    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }


    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class);
    }
    public function attendance()
    {
        return $this->hasMany(Attendancee::class);
    }

    public function getCurrentSession()
    {
        return $this->where('is_current', true)->first();
    }
    public function getPreviousSessions()
    {
        return $this->where('is_current', false)->get();
    }
    public function getActiveSession()
    {
        return $this->where('is_current', true)->orWhere('is_current', false)->first();
    }

    public function getSemesterCourses($semesterId)
    {
        return SemesterCourseRegistration::where('semester_id', $semesterId)->get();
    }

    public function getSemesterCourseRegistrationsByCourse($courseId)
    {
        return SemesterCourseRegistration::where('course_id', $courseId)->get();
    }

    public function getSemesterCourseRegistrationsByStudent($studentId)
    {
        return SemesterCourseRegistration::where('student_id', $studentId)->get();
    }

    public function timetables()
    {
        return $this->hasMany(TimeTable::class);
    }








    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];
}
