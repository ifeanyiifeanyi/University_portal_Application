<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TicketPriorityUpdateNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $oldPriority;
    protected $newPriority;

    public function __construct(Ticket $ticket, string $oldPriority, string $newPriority)
    {
        $this->ticket = $ticket;
        $this->oldPriority = $oldPriority;
        $this->newPriority = $newPriority;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Priority Updated for Ticket #{$this->ticket->ticket_number}")
            ->line("The priority of your support ticket has been updated.")
            ->line("Ticket: {$this->ticket->subject}")
            ->line("Previous Priority: " . ucfirst($this->oldPriority))
            ->line("New Priority: " . ucfirst($this->newPriority))
            ->action('View Ticket', url("/tickets/{$this->ticket->id}"))
            ->line('Thank you for your patience.');
    }

    public function toArray($notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'old_priority' => $this->oldPriority,
            'new_priority' => $this->newPriority,
            'message' => "Ticket priority updated from {$this->oldPriority} to {$this->newPriority}"
        ];
    }
}
