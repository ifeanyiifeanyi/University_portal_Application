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
        'status',
        'grade_point'
    ];

    protected $casts = [
        'assessment_score' => 'integer',
        'exam_score' => 'integer',
        'total_score' => 'integer',
        'is_failed' => 'boolean',
        'grade_point' => 'decimal:2',
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

    // this is used for audit modal to keep track of score/assessments
    // public function getDirty()
    // {
    //     $dirty = parent::getDirty();
    //     $original = $this->getOriginal();

    //     foreach ($dirty as $key => $value) {
    //         $dirty[$key] = [
    //             'from' => $original[$key] ?? null,
    //             'to' => $value
    //         ];
    //     }

    //     return $dirty;
    // }

    
}
