<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'ticket_attachments';

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    
}
