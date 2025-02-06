<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'faculty_id',
        'description',
        'duration',
        'phone',
        'email',
        'program_id',
        'department_head_id',
        'level_format'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function departmentHead()
    {
        return $this->belongsTo(User::class, 'department_head_id');
    }

    public function timetables()
    {
        return $this->hasMany(TimeTable::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function paymentTypes()
    {
        return $this->belongsToMany(PaymentType::class)->withPivot('level');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }


    // Retrieve level in display format
    public function getCurrentLevelAttribute($value)
    {
        return $this->getDisplayLevel($value);
    }

    public function getLevelsAttribute()
    {
        // Always return display format for consistency
        if (!$this->level_format) {
            return range(100, $this->duration * 100, 100);
        }

        switch ($this->level_format) {
            case 'nd_hnd':
                return ['ND1', 'ND2', 'HND1', 'HND2'];
            case 'nursing':
                return ['RN1', 'RN2', 'RN3'];
            case 'midwifery':
                return ['RMW1', 'RMW2', 'RMW3'];
            default:
                return range(100, $this->duration * 100, 100);
        }
    }

    public function getLevelNumber($levelString)
    {
        $levelMappings = [
            // ND/HND mappings
            'ND1' => 100,
            'ND2' => 200,
            'HND1' => 300,
            'HND2' => 400,
            // Nursing mappings
            'RN1' => 100,
            'RN2' => 200,
            'RN3' => 300,
            // Midwifery mappings
            'RMW1' => 100,
            'RMW2' => 200,
            'RMW3' => 300
        ];

        // If it's already numeric, return as integer
        if (is_numeric($levelString)) {
            return (int)$levelString;
        }

        // Return mapped value or null if not found
        return $levelMappings[$levelString] ?? null;
    }


    // Helper method to get display format for a numeric level
    // Convert numeric to display format
    public function getDisplayLevel($numericLevel)
    {
        if (!$this->level_format) {
            return $numericLevel;
        }

        $reverseMappings = [
            'nd_hnd' => [
                100 => 'ND1',
                200 => 'ND2',
                300 => 'HND1',
                400 => 'HND2'
            ],
            'nursing' => [
                100 => 'RN1',
                200 => 'RN2',
                300 => 'RN3'
            ],
            'midwifery' => [
                100 => 'RMW1',
                200 => 'RMW2',
                300 => 'RMW3'
            ]
        ];

        return $reverseMappings[$this->level_format][$numericLevel] ?? $numericLevel;
    }


    public function isValidLevel($level)
    {
        return in_array($level, $this->levels);
    }

    public function isValidNumericLevel($numericLevel)
    {
        return in_array($this->getDisplayLevel($numericLevel), $this->levels);
    }

    // Store level in numeric format
    public function setCurrentLevelAttribute($value)
    {
        $this->attributes['current_level'] = $this->getLevelNumber($value);
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

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_assignments')
            ->withPivot('academic_session_id', 'semester_id', 'course_id')
            ->withTimestamps();
    }
    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class);
    }


    public function semesters()
    {
        return $this->belongsToMany(Semester::class, 'department_semester')
            ->withPivot('max_credit_hours', 'level')
            ->withTimestamps();
    }

    // this builds a relationship between courses student registers for
    public function courseEnrollments()
    {
        return $this->hasManyThrough(CourseEnrollment::class, Student::class);
    }
}
