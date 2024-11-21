<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Program extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',        // Regular, Part-time, Evening, Weekend, etc.
        'code',
        'description',
        'class_schedule_type',  // Morning, Evening, Weekend, Flexible
        'duration_type',        // Years, Semesters, Months
        'duration_value',       // Numeric value of duration
        'attendance_requirement', // Minimum attendance percentage
        'status',
        'tuition_fee_multiplier', // Fee multiplier compared to regular program
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
