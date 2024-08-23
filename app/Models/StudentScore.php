<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentScore extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'course_id',
        'teacher_id',
        'academic_session_id',
        'semester_id',
        'department_id',
        'assessment_score',
        'exam_score',
        'total_score',
        'grade',
        'is_failed',
        'status'
    ];

    protected $casts = [
        'is_failed' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function audits()
    {
        return $this->hasMany(ScoreAudit::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
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

    public function scoreAudits()
    {
        return $this->hasMany(ScoreAudit::class);
    }
}
