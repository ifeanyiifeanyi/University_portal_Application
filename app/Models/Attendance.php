<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'teacher_id',
        'academic_session_id',
        'semester_id',
        'department_id',
        'date',
        'is_present'
    ];
    protected $casts = [
        'is_present' => 'boolean',
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

    // public function attendanceDetails(){
    //     return $this->hasMany(AttendanceDetail::class);
    // }
    


}
