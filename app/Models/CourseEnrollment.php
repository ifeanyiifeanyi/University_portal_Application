<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


// REMEMBER THIS IS USED BY THE ADMIN TO REGISTER COURSES FOR THE STUDENT
class CourseEnrollment extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'course_id',
        'department_id',
        'level',
        'status',
        'semester_course_registration_id',
        'academic_session_id',
        'grade',
        'score',
        'registered_at',
        'is_carryover'
    ];
    

    protected $casts = [
        'registered_at' => 'datetime',
        'score' => 'float',
    ];

    // Define possible statuses
    const STATUS_ENROLLED = 'enrolled';
    const STATUS_WITHDRAWN = 'withdrawn';
    const STATUS_COMPLETED = 'completed';



    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function semesterCourseRegistration()
    {
        return $this->belongsTo(SemesterCourseRegistration::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    // Scopes for easy querying
    public function scopeEnrolled($query)
    {
        return $query->where('status', self::STATUS_ENROLLED);
    }

    public function scopeWithdrawn($query)
    {
        return $query->where('status', self::STATUS_WITHDRAWN);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // Helper methods
    public function isEnrolled()
    {
        return $this->status === self::STATUS_ENROLLED;
    }

    public function isWithdrawn()
    {
        return $this->status === self::STATUS_WITHDRAWN;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function withdraw()
    {
        $this->status = self::STATUS_WITHDRAWN;
        $this->save();
    }

    public function complete()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->save();
    }

    public function setGrade($grade)
    {
        $this->grade = $grade;
        $this->save();
    }

    public function setScore($score)
    {
        $this->score = $score;
        $this->save();
    }

    // Custom methods
    public function getCreditHours()
    {
        return $this->course->credit_hours;
    }

    public function getGradePoint()
    {
        // Implement your grading system here
        // This is just an example
        switch ($this->grade) {
            case 'A':
                return 4.0;
            case 'B':
                return 3.0;
            case 'C':
                return 2.0;
            case 'D':
                return 1.0;
            default:
                return 0.0;
        }
    }

    public function getWeightedGradePoint()
    {
        return $this->getGradePoint() * $this->getCreditHours();
    }
}
