<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewTicketReplyNotification extends Notification
{
    use Queueable;

    protected $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }


    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Reply on Ticket #' . $this->ticket->ticket_number)
            ->line('A student has replied to ticket #' . $this->ticket->ticket_number)
            ->line('Subject: ' . $this->ticket->subject)
            ->action('View Ticket', route('admin.support_tickets.show', $this->ticket))
            ->line('Please respond to this ticket as soon as possible.');
    }

    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'subject' => $this->ticket->subject,
            'student_name' => $this->ticket->user->full_name,
            'message' => 'New reply from student on ticket #' . $this->ticket->id
        ];
    }
}
