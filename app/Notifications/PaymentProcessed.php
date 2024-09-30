<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentProcessed extends Notification
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
            ->subject('Payment Processed')
            ->line('Your payment has been processed successfully.')
            ->line('Payment Details:')
            ->line('Amount: ' . $this->payment->amount)
            ->line('Payment Type: ' . $this->payment->paymentType->name)
            ->line('Transaction Reference: ' . $this->payment->transaction_reference)
            ->action('View Receipt', route('admin.payments.showReceipt', $this->payment->receipt->id))
            ->line('Thank you for your payment!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'payment_type' => $this->payment->paymentType->name,
            'transaction_reference' => $this->payment->transaction_reference,
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
