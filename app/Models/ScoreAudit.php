<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoreAudit extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_score_id',
        'user_id',
        'action',
        'comment',
        'old_value',
        'new_value',
        'changed_fields',
        'ip_address'
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'changed_fields' => 'array',
    ];


    public function studentScore()
    {
        return $this->belongsTo(StudentScore::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course(){
        return $this->belongsTo(Course::class);
    }
    public function academicSession(){
        return $this->belongsTo(AcademicSession::class);
    }

    public function semester(){
        return $this->belongsTo(Semester::class);
    }

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }


}
