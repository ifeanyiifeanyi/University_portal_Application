<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courseregistration extends Model
{
    use HasFactory;
    protected $table = "course_registrations";
    protected $fillable = [
        'course_id', 
        'department_id', 
        'semester_id', 
        'session_id',
        'user_id',
        'student_id',
        'level',
        'status',
        'semester_regid'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class,);
    }
    						
}
