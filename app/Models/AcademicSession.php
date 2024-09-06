<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_date', 'end_date', 'is_current'];
    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }


    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class);
    }
    public function attendance()
    {
        return $this->hasMany(Attendancee::class);
    }






    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];
}
