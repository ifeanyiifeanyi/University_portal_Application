<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\BelongsTo;
=======
>>>>>>> origin/master

class Teacher extends Model
{
    use HasFactory;
<<<<<<< HEAD
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
    											
=======
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }
>>>>>>> origin/master
}
