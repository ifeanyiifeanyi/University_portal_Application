<?php

namespace App\Models;

use App\Models\Semester;
use App\Models\AcademicSession;
use App\Trait\StudentDebtTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;
    use StudentDebtTrait;
    use SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function getPaymentsByYear($year)
    {
        $monthlyFee = 35000; // Naira

        return $this->recurringSubscriptions()
            ->whereYear('created_at', $year)
            ->where('amount_paid', '>', 0) // Only fetch payments greater than zero
            ->get()
            ->map(function ($subscription) use ($monthlyFee) {
                $paymentDetails = $this->calculatePaidMonths($subscription, $monthlyFee);

                return [
                    'student_name' => $this->user->full_name,
                    'student_level' => $this->current_level,
                    'phone_number' => $this->user->phone ?? 'N/A',
                    'email' => $this->user->email ?? 'N/A',
                    'amount_paid' => $subscription->amount_paid,
                    'total_amount' => $subscription->total_amount,
                    'number_of_months' => $paymentDetails['months_count'],
                    'months_list' => $paymentDetails['months'],
                    'payment_date' => $subscription->created_at,
                    'start_date' => $subscription->start_date,
                    'end_date' => $subscription->end_date
                ];
            });
    }

    private function calculatePaidMonths($subscription, $monthlyFee)
    {
        // Use number_of_payments if available, otherwise calculate from amount_paid
        $monthsCount = $subscription->number_of_payments ?? floor($subscription->amount_paid / $monthlyFee);

        if ($monthsCount <= 0) {
            return [
                'months_count' => 0,
                'months' => []
            ];
        }

        $months = [];
        $startDate = \Carbon\Carbon::parse($subscription->start_date);

        // Generate month names based on start date
        for ($i = 0; $i < $monthsCount; $i++) {
            $monthDate = $startDate->copy()->addMonths($i);
            $months[] = [
                'name' => $monthDate->format('F'),
                'year' => $monthDate->year
            ];
        }

        return [
            'months_count' => $monthsCount,
            'months' => $months
        ];
    }

    // Add relationship to Student model
    public function emails()
    {
        return $this->hasMany(StudentEmail::class);
    }

    public function getDebtDetailsAttribute()
    {
        $currentSession = AcademicSession::where('is_current', true)->first();
        $currentSemester = Semester::where('is_current', true)->first();

        return $this->calculateDebt($currentSession, $currentSemester);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCurrentLevelAttribute($value)
    {
        return $this->department->getDisplayLevel($value);
    }

    public function setCurrentLevelAttribute($value)
    {
        $this->attributes['current_level'] = $this->department->getLevelNumber($value);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // public function courses()
    // {
    //     return $this->belongsToMany(Course::class, 'enrollments')
    //         ->withPivot('assessment_score', 'exam_score', 'grade', 'semester_id')
    //         ->withTimestamps();
    // }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function scores()
    {
        return $this->hasMany(StudentScore::class);
    }

    public function scoreAudits()
    {
        return $this->hasManyThrough(ScoreAudit::class, StudentScore::class);
    }

    /**
     * Get all semester registrations for the student.
     */
    public function semesterRegistrations(): HasMany
    {
        return $this->hasMany(SemesterCourseRegistration::class);
    }


    public function getAuditsBySessionAndSemester()
    {
        return $this->scoreAudits()
            ->join('student_scores as ss1', 'score_audits.student_score_id', '=', 'ss1.id')
            ->join('academic_sessions', 'ss1.academic_session_id', '=', 'academic_sessions.id')
            ->join('semesters', 'ss1.semester_id', '=', 'semesters.id')
            ->select('score_audits.*', 'academic_sessions.name as session_name', 'semesters.name as semester_name', 'ss1.teacher_id as laravel_through_key')
            ->where('ss1.teacher_id', 20)
            ->orderBy('academic_sessions.name', 'desc')
            ->orderBy('semesters.name', 'asc')
            ->get()
            ->groupBy(['session_name', 'semester_name']);
    }


    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function recurringSubscriptions()
    {
        return $this->hasMany(StudentRecurringSubscription::class);
    }
}
