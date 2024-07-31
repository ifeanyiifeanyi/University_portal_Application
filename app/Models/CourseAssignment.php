<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'department_id', 'semester_id', 'level'];

    public function course()
    {
        return $this->belongsTo(Course::class,);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
