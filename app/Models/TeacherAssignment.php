<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAssignment extends Model
{
    use HasFactory;
    // protected $fillable = ['teacher_id', 'course_assignment_id', 'academic_session_id', 'semester_id'];
    protected $fillable = ['teacher_id', 'department_id', 'academic_session_id', 'semester_id', 'course_id'];

    // In TeacherAssignment model
    public function getFormattedLevelAttribute()
    {
        $courseAssignment = $this->course->courseAssignments
            ->where('department_id', $this->department_id)
            ->where('semester_id', $this->semester_id)
            ->first();

        if ($courseAssignment) {
            return $this->department->getDisplayLevel($courseAssignment->level);
        }

        return null;
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    // app/Models/TeacherAssignment.php

    public function courseAssignment()
    {
        return $this->belongsTo(CourseAssignment::class, 'course_id', 'course_id')
            ->where('semester_id', $this->semester_id);
    }
}
