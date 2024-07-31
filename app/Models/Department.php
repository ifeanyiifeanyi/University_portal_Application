<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'faculty_id', 'description'];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function getLevelsAttribute()
    {
        return range(100, $this->duration * 100, 100);
    }

    public function courseAssignments()
    {
        return $this->hasMany(CourseAssignment::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_assignments')
            ->withPivot('semester_id', 'level')
            ->withTimestamps();
    }
}
