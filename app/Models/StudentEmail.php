<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentEmail extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'attachments' => 'array',
        'sent_at' => 'datetime',
    ];

     /**
     * Get the student that received the email
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the admin who sent the email
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

     /**
     * Get the sender user record
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

}
