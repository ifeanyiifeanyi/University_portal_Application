<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TicketStatusUpdateNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(Ticket $ticket, string $oldStatus, string $newStatus)
    {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Ticket #{$this->ticket->ticket_number} Status Updated")
            ->line("Your support ticket status has been updated from " .
                  ucfirst(str_replace('_', ' ', $this->oldStatus)) .
                  " to " . ucfirst(str_replace('_', ' ', $this->newStatus)) . ".")
            ->line("Ticket Subject: {$this->ticket->subject}")
            ->action('View Ticket', route('student.support-tickets.show', $this->ticket))
            ->line('Thank you for your patience.');
    }

    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "Ticket status updated to " . ucfirst(str_replace('_', ' ', $this->newStatus)),
        ];
    }
}
