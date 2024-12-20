<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentApprovedNotification extends Notification
{
    use Queueable;
    protected $invoice;

    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }


    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->greeting('Hello ' . $notifiable->fullName())
            ->line('Your payment for Invoice #' . $this->invoice->invoice_number . ' has been successfully approved.')
            ->line('Thank you for your payment.')
            ->action('View Receipt', url('/student/invoice/' . $this->invoice->id));
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Your payment for Invoice #' . $this->invoice->invoice_number . ' has been approved.',
            'invoice_id' => $this->invoice->id,
        ];
    }
}
