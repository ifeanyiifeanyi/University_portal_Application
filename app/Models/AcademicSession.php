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
    public function semesterRegistration()
    {
        return $this->belongsTo(SemesterRegistration::class);
    }
}
