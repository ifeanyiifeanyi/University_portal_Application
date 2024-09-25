<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminPaymentNotification extends Notification
{
    use Queueable;

    protected $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
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
            ->subject('New Payment Processed')
            ->line('A new payment has been processed.')
            ->line('Payment Details:')
            ->line('Student: ' . $this->payment->student->user->full_name)
            ->line('Amount: ' . $this->payment->amount)
            ->line('Payment Type: ' . $this->payment->paymentType->name)
            ->line('Transaction Reference: ' . $this->payment->transaction_reference)
            ->line('Payment Status: ' . $this->payment->status)
            ->line('Invoice Status: ' . ($this->payment->invoice ? $this->payment->invoice->status : 'N/A'))
            ->action('View Payment Details', route('admin.payments.show', $this->payment->id));
    }

    public function toDatabase($notifiable)
    {
        return [
            'payment_id' => $this->payment->id,
            'student_name' => $this->payment->student->user->full_name,
            'amount' => $this->payment->amount,
            'payment_type' => $this->payment->paymentType->name,
            'transaction_reference' => $this->payment->transaction_reference,
            'payment_status' => $this->payment->status,
            'invoice_status' => $this->payment->invoice ? $this->payment->invoice->status : 'N/A',
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
