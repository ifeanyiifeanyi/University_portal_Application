<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentsParent extends Model
{
    use HasFactory;
    protected $table = "student_parents";

     /**
     * The parent associated with this record.
     */
    public function parent()
    {
        return $this->belongsTo(Parents::class);
    }

    /**
     * The student associated with this record.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
