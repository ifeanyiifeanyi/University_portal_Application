<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'faculty_id', 'description'];

    public function faculty(){
        return $this->belongsTo(Faculty::class);
    }
}
