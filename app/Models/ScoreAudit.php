<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoreAudit extends Model
{
    use HasFactory;
    protected $fillable = ['student_score_id', 'user_id', 'action', 'comment'];

    public function studentScore()
    {
        return $this->belongsTo(StudentScore::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
