<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemesterRegistration extends Model
{
    use HasFactory;
    protected $fillable = ['semester_id', 'academic_session_id', 'level', 'user_id', 'student_id'];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    public function AcademicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }
}
