<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFailedCourse extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'course_id',
        'academic_session_id',
        'semester_id',
        'department_id',
        'student_score_id',
        'is_retaken'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function studentScore()
    {
        return $this->belongsTo(StudentScore::class);
    }
}
