<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function timetables()
    {
        return $this->hasMany(TimeTable::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTitleAndFullNameAttribute()
    {
        $user = $this->user;
        $fullName = $user->first_name . ' ' . ($user->other_name ?? '') . ' ' . $user->last_name;

        return $this->teacher_title . ' ' . trim($fullName);
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


        // $query = ScoreAudit::select('score_audits.*', 'academic_sessions.name as session_name', 'semesters.name as semester_name', 'student_scores.teacher_id as laravel_through_key')
        //     ->join('student_scores', 'student_scores.id', '=', 'score_audits.student_score_id')
        //     ->join('student_scores as ss', 'score_audits.student_score_id', '=', 'ss.id')
        //     ->join('academic_sessions', 'ss.academic_session_id', '=', 'academic_sessions.id')
        //     ->join('semesters', 'ss.semester_id', '=', 'semesters.id')
        //     ->where('ss.teacher_id', 20)
        //     ->orderBy('academic_sessions.name', 'desc')
        //     ->orderBy('semesters.name', 'asc')
        //     ->get();
    }
    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }
}
