<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = [
       'date_of_birth',
       'gender',
       'teaching_experience',
       'teacher_type',
       'teacher_qualification',
       'teacher_title',
       'employment_id',
       'date_of_employment',
       'address',
       'nationality',
       'level'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    											
}
