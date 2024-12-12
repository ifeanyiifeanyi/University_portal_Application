<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\TicketResponse;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;

// class TicketResponseMail extends Mailable implements ShouldQueue
// {
//     use Queueable, SerializesModels;

//     /**
//      * Number of times the job should be attempted.
//      *
//      * @var int
//      */
//     public $tries = 3;

//     /**
//      * The number of seconds the job can run before timing out.
//      *
//      * @var int
//      */
//     public $timeout = 60;

//     /**
//      * Create a new message instance.
//      */
//     public $ticket;
//     public $response;
//     public function __construct(

//         Ticket $ticket,

//         TicketResponse $response,
//     ) {
//         $this->ticket = $ticket;
//         $this->response = $response;
//     }

//     /**
//      * Get the message envelope.
//      */
//     public function envelope(): Envelope
//     {
//         return new Envelope(
//             subject: "Re: [Ticket #{$this->ticket->ticket_number}] {$this->ticket->subject}",
//         );
//     }

//     /**
//      * Get the message content definition.
//      */
//     public function content(): Content
//     {
//         return new Content(
//             view: 'emails.ticket-response',
//             with: [
//                 'ticketNumber' => $this->ticket->ticket_number,
//                 'subject' => $this->ticket->subject,
//                 'response' => $this->response, // Pass the entire response object
//                 'respondentName' => $this->response->admin?->user?->full_name ?? 'Support Team',
//                 'department' => $this->ticket->department->name,
//             ],
//         );
//     }

//     /**
//      * Get the message headers.
//      */
//     public function headers(): Headers
//     {
//         $references = [];
//         if ($this->response->in_reply_to) {
//             $references[] = $this->response->in_reply_to;
//         }

//         return new Headers(
//             messageId: $this->response->email_message_id,
//             references: $references,
//             text: [
//                 'X-Ticket-ID' => $this->ticket->ticket_number,
//                 'In-Reply-To' => $this->response->in_reply_to ?: null,
//             ]
//         );
//     }

//     /**
//      * Get the attachments for the message.
//      *
//      * @return array<int, \Illuminate\Mail\Mailables\Attachment>
//      */
//     public function attachments(): array
//     {
//         return [];
//     }
// }

class TicketResponseMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $ticket;
    public $responses;
    public $respondentName;

    public function __construct(Ticket $ticket, Collection $responses)
    {
        $this->ticket = $ticket;
        $this->responses = $responses;
        $this->respondentName = $responses->first()->admin?->user?->full_name ?? 'Support Team';
    }

    public function envelope(): Envelope
    {
        // Convert the references array to a string
        $references = $this->responses->pluck('email_message_id')->join(' ');

        return new Envelope(
            subject: "Re: [Ticket #{$this->ticket->ticket_number}] {$this->ticket->subject}",
            metadata: [
                'ticket_id' => (string) $this->ticket->ticket_number,
                'references' => $references,
            ]
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-response',
            with: [
                'ticketNumber' => $this->ticket->ticket_number,
                'subject' => $this->ticket->subject,
                'responses' => $this->responses,
                'respondentName' => $this->respondentName,
                'department' => $this->ticket->department->name,
                'ticket' => $this->ticket,
            ],
        );
    }

    public function headers(): Headers
    {
        // Collect all previous message IDs as references
        $references = $this->responses->pluck('in_reply_to')->filter()->toArray();

        return new Headers(
            messageId: $this->responses->first()->email_message_id,
            references: $references,
            text: [
                'X-Ticket-ID' => $this->ticket->ticket_number,
                'X-Questions-Answered' => $this->responses->pluck('question_id')->join(','),
            ],
        );
    }
}
