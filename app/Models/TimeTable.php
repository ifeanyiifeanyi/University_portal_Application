<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeTable extends Model
{
    use HasFactory;
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_APPROVED = 'approved';
    const STATUS_ARCHIVED = 'archived';


    protected $fillable = [
        'academic_session_id',
        'semester_id',
        'department_id',
        'level',
        'day_of_week',
        'start_time',
        'end_time',
        'course_id',
        'teacher_id',
        'room',
        'status',
        'created_by',
        'updated_by',
        'class_duration',
        'is_current',
        'class_date'
    ];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'day_of_week' => 'integer',
        'level' => 'integer',
        'class_duration' => 'integer',
        'is_current' => 'boolean',
        'class_date' => 'date'

    ];
    public function getClassDateAttribute(){
        return $this->class_date->format('Y-m-d');
    }

    public function getDurationAttribute()
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    public function getWeeklyHoursAttribute()
    {
        return $this->duration / 60;
    }
    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function hasConflict(Timetable $other)
    {
        return $this->day_of_week === $other->day_of_week &&
            $this->start_time < $other->end_time &&
            $this->end_time > $other->start_time &&
            (($this->room === $other->room) ||
                ($this->teacher_id === $other->teacher_id) ||
                ($this->course_id === $other->course_id &&
                    $this->level === $other->level &&
                    $this->department_id === $other->department_id));
    }

    public static function getDayName($day)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        return $days[$day - 1] ?? '';
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isEditable()
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PENDING_APPROVAL]);
    }
}
