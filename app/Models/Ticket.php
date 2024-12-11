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

    public function attachments(){
        return $this->hasMany(TicketAttachment::class);
    }


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function responses(){
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


}
