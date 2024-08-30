<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // public function courses()
    // {
    //     return $this->belongsToMany(Course::class, 'enrollments')
    //         ->withPivot('assessment_score', 'exam_score', 'grade', 'semester_id')
    //         ->withTimestamps();
    // }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function scores()
    {
        return $this->hasMany(StudentScore::class);
    }

    public function scoreAudits()
    {
        return $this->hasManyThrough(ScoreAudit::class, StudentScore::class);
    }



    public function getAuditsBySessionAndSemester()
    {
        return $this->scoreAudits()
            ->join('student_scores as ss1', 'score_audits.student_score_id', '=', 'ss1.id')
            ->join('academic_sessions', 'ss1.academic_session_id', '=', 'academic_sessions.id')
            ->join('semesters', 'ss1.semester_id', '=', 'semesters.id')
            ->select('score_audits.*', 'academic_sessions.name as session_name', 'semesters.name as semester_name', 'ss1.teacher_id as laravel_through_key')
            ->where('ss1.teacher_id', 20)
            ->orderBy('academic_sessions.name', 'desc')
            ->orderBy('semesters.name', 'asc')
            ->get()
            ->groupBy(['session_name', 'semester_name']);

        // return $this->scoreAudits()
        //     ->join('student_scores', 'score_audits.student_score_id', '=', 'student_scores.id')
        //     ->join('academic_sessions', 'student_scores.academic_session_id', '=', 'academic_sessions.id')
        //     ->join('semesters', 'student_scores.semester_id', '=', 'semesters.id')
        //     ->select('score_audits.*', 'academic_sessions.name as session_name', 'semesters.name as semester_name')
        //     ->orderBy('academic_sessions.name', 'desc')
        //     ->orderBy('semesters.name', 'asc')
        //     ->get()
        //     ->groupBy(['session_name', 'semester_name']);
    }


    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }
}
