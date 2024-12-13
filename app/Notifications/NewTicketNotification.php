<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewTicketNotification extends Notification
{
    use Queueable;
    protected $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Support Ticket: ' . $this->ticket->ticket_number)
            ->line('A new support ticket has been created.')
            ->line('Subject: ' . $this->ticket->subject)
            ->line('Priority: ' . ucfirst($this->ticket->priority))
            ->action('View Ticket', route('admin.support_tickets.show', $this->ticket))
            ->line('Please respond within the SLA timeline.');
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'priority' => $this->ticket->priority,
            'category' => $this->ticket->category,
        ];
    }
}
