<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemesterCourseRegistration extends Model
{
    use HasFactory;
    protected $fillable = [
        'semester_id',
        'academic_session_id',
        'student_id',
        'status',
        'total_credit_hours'
    ];

    protected $casts = [
        'total_credit_hours' => 'integer',
    ];

    // Define possible statuses
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    // Custom methods
    public function updateTotalCreditHours()
    {
        $this->total_credit_hours = $this->courseEnrollments()
            ->join('courses', 'course_enrollments.course_id', '=', 'courses.id')
            ->sum('courses.credit_hours');
        $this->save();
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function approve()
    {
        $this->status = self::STATUS_APPROVED;
        $this->save();
    }

    public function reject()
    {
        $this->status = self::STATUS_REJECTED;
        $this->save();
    }

    // Scopes for easy querying
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
}
