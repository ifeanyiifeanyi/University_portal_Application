<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class BackupCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected string $backupPath, protected string $type) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: ucfirst($this->type) . ' Backup Created');
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {

        return new Content(
            view: 'emails.backup-created',
            with: [
                'type' => $this->type,
                'fileName' => basename($this->backupPath),
                'size' => $this->getFileSize($this->backupPath),
                'createdAt' => now()->format('Y-m-d H:i:s')
            ]
        );
    }


    public function attachments(): array
    {
        // Only attach if file exists and is not too large (e.g., < 25MB)
        if (file_exists($this->backupPath) && filesize($this->backupPath) < 25000000) {
            return [
                Attachment::fromPath($this->backupPath)
                    ->as(basename($this->backupPath))
                    ->withMime('application/zip'),
            ];
        }
        return [];
    }

    private function getFileSize($path): string
    {
        $bytes = file_exists($path) ? filesize($path) : 0;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
