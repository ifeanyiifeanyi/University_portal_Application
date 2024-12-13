<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable = ['ticket_number', 'subject', 'category', 'user_id', 'department_id', 'description', 'status', 'priority'];


    public function questions()
    {
        return $this->hasMany(TicketQuestion::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responses()
    {
        return $this->hasMany(TicketResponse::class);
    }

    public function suggestedResponses()
    {
        return $this->hasMany(AiSuggestedResponse::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }


    // piority scopes
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeMediumPriority($query)
    {
        return $query->where('priority', 'medium');
    }

    public function scopeLowPriority($query)
    {
        return $query->where('priority', 'low');
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
}
