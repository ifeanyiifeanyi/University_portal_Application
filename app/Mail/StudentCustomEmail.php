<?php

namespace App\Mail;

use App\Models\Student;
use App\Models\StudentEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;

class StudentCustomEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Student $student,
        public string $emailSubject,
        public string $emailMessage,
        public array $emailAttachments,
        public StudentEmail $studentEmail
    ) {}


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            to: [new Address($this->student->user->email, $this->student->user->fullName())]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student-custom',
            with: [
                'messageContent' => $this->emailMessage,
                'studentName' => $this->student->user->fullName()
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return collect($this->emailAttachments)
            ->map(fn($path) => Attachment::fromPath(storage_path('app/public/' . $path)))
            ->all();
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        return new Headers(
            messageId: null,
            references: []
        );
    }

    /**
     * Handle a successful message send
     */
    public function sent(): void
    {
        $this->studentEmail->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }
    /**
     * Handle a failed message
     */
    public function failed(\Throwable $exception): void
    {
        $this->studentEmail->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage()
        ]);
    }
}
