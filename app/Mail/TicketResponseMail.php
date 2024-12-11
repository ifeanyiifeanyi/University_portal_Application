<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\TicketResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;

class TicketResponseMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Number of times the job should be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * Create a new message instance.
     */
    public function __construct(

         Ticket $ticket,

         TicketResponse $response,
    ) {
        $this->ticket = $ticket;
        $this->response = $response;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Re: [Ticket #{$this->ticket->ticket_number}] {$this->ticket->subject}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-response',
            with: [
                'ticketNumber' => $this->ticket->ticket_number,
                'subject' => $this->ticket->subject,
                'response' => $this->response->response,
                'respondentName' => $this->response->admin?->user?->full_name ?? 'Support Team',
                'department' => $this->ticket->department->name,
            ],
        );
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        $references = $this->response->in_reply_to ? [$this->response->in_reply_to] : [];

        return new Headers(
            messageId: $this->response->email_message_id,
            references: $references,
            text: [
                'In-Reply-To' => $this->response->in_reply_to,
                'X-Ticket-ID' => $this->ticket->ticket_number,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
