<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }

    // public function subjects(){
    //     return $this->belongsToMany(Subject::class,'student_subjects','student_id','subject_id');
    // }

    public function department(){
        return $this->belongsTo(Department::class);
    }
}
