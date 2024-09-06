<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeSystem extends Model
{
    use HasFactory;
    protected $fillable = ['grade', 'min_score', 'max_score'];

    public static function getGrade($score)
    {
        return self::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first()->grade;
    }

}
