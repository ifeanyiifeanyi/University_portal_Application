<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketResponse extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id', 'question_id', 'admin_id', 'response', 'is_ai_response', 'email_message_id', 'in_reply_to', 'sent_at'];

    protected $cast =[
        'is_ai_response' => 'boolean',
        'sent_at' => 'datetime'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function question()
    {
        return $this->belongsTo(TicketQuestion::class);
    }

    public function staff()
    {
        return $this->belongsTo(Admin::class);
    }
}
