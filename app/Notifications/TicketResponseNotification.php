<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;

class TicketResponseNotification extends Notification
{
    use Queueable;
    protected $ticket;
    protected $responses;

    public function __construct(Ticket $ticket, Collection $responses)
    {
        $this->ticket = $ticket;
        $this->responses = $responses;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject("New Response to Ticket #{$this->ticket->ticket_number}")
            ->line("You have received new responses to your support ticket.")
            ->line("Ticket Subject: {$this->ticket->subject}");

        foreach ($this->responses as $response) {
            $mailMessage->line("Question: {$response->question->question}")
                ->line("Response: {$response->response}")
                ->line('---');
        }

        return $mailMessage
            ->action('View Ticket', route('student.support-tickets.show', $this->ticket))
            ->line('If you have any further questions, please reply to this ticket.');
    }

    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'response_count' => $this->responses->count(),
            'message' => 'New response(s) to your ticket',
        ];
    }
}
