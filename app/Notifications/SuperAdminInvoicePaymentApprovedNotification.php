<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SuperAdminInvoicePaymentApprovedNotification extends Notification
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
            ->greeting('Hello Super Admin')
            ->line('Payment for Invoice #' . $this->invoice->invoice_number . ' has been marked as paid by an admin.')
            ->action('View Payment', url('/admin/invoice-manager/' . $this->invoice->id.'/details'));
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Payment for Invoice #' . $this->invoice->invoice_number . ' has been marked as paid by an admin.',
            'invoice_id' => $this->invoice->id,
        ];
    }
}
