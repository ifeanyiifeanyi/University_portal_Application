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
        'is_failed', 'status'
    ];

    protected $casts = [
        'is_failed' => 'boolean',
    ];
}
