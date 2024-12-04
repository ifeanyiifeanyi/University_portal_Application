<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSuggestedResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'question_id',
        'suggested_response',
        'was_used',
        'admin_id',
        'used_at'
    ];

    protected $casts = [
        'was_used' => 'boolean',
        'used_at' => 'datetime'
    ];
}
