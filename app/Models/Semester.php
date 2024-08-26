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
            ->withPivot('max_credit_hours', 'level')
            ->withTimestamps();
    }

    public function canBeDeleted()
    {
        return !$this->is_current && !$this->courseAssignments()->exists() && !$this->teacherAssignments()->exists();
    }

    public function getCourseAssignmentsByCourse($courseId)
    {
        return CourseAssignment::where('course_id', $courseId)->where('semester_id', $this->id)->get();
    }

    public function getCourseAssignmentsByTeacher($teacherId)
    {
        return TeacherAssignment::where('teacher_id', $teacherId)->where('semester_id', $this->id)->get();
    }

    public static function getCurrentSemester()
    {
        return self::where('is_current', true)->first();
    }
    public function getPreviousSemesters(){
        return self::where('is_current', false)->get();
    }
    public function getActiveSemester(){
        return self::where('is_current', true)->orWhere('is_current', false)->first();
    }


    protected $casts = [
        // 'start_date' => 'date',
        // 'end_date' => 'date',
        'is_current' => 'boolean',
    ];
}
