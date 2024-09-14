<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentType extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'amount', 'description', 'is_active', 'academic_session_id', 'semester_id', 'slug'];

    public function departments()
    {
        return $this->belongsToMany(Department::class)->withPivot('level');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function semester(){
        return $this->belongsTo(Semester::class);
    }

    public function academicSession(){
        return $this->belongsTo(AcademicSession::class);
    }

    protected $casts = ['is_active' => 'boolean'];
}
