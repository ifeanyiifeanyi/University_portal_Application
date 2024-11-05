<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'teacher_id',
        'academic_session_id',
        'semester_id',
        'department_id',
        'date',
        'start_time',
        'end_time',
        'notes'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }

    public function academicSession(){
        return $this->belongsTo(AcademicSession::class);
    }

    public function semester(){
        return $this->belongsTo(Semester::class);
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }

    // Add this new relationship
    public function studentAttendances() {
        return $this->hasMany(Attendancee::class, 'attendance_id');
    }
}