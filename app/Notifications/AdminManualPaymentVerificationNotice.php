<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AdminManualPaymentVerificationNotice extends Notification
{
    use Queueable;

    protected $payment;
    protected $status;

    public function __construct(Payment $payment, string $status)
    {
        $this->payment = $payment;
        $this->status = $status;
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
    public function toMail(object $notifiable): MailMessage
    {
        $amount = '₦' . number_format($this->payment->base_amount, 2);
        $date = now()->format('d M, Y h:i A');

        return (new MailMessage)
            ->subject('Manual Payment Verification Completed')
            ->greeting('Hello ADMIN,')
            ->line("The manual payment submitted by {$this->payment->student->user->full_name} has been processed.")
            ->line("Transaction Reference: {$this->payment->transaction_reference}")
            ->line("Amount: {$amount}")
            ->line("Verification Status: " . ucfirst($this->status))
            ->line("Date Verified: {$date}")
            ->action('View Payment Details', url("/"))
            ->line('Thank you for your diligence.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'transaction_reference' => $this->payment->transaction_reference,
            'amount' => '₦' . number_format($this->payment->base_amount, 2),
            'status' => $this->status,
            'verified_at' => now()->format('d M, Y h:i A'),
            'title' => 'Payment Verification Completed',
            'message' => "Payment verification completed for {$this->payment->student->user->full_name}",
            'link' => "/admin/payments/{$this->payment->id}",
            'type' => 'payment_verification'
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
