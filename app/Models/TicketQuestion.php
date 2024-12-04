<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id', 'question', 'email_message_id'];

    public function ticket(){
        return $this->belongsTo(Ticket::class);
    }

    public function responses()
    {
        return $this->hasMany(TicketResponse::class, 'question_id');
    }
}
