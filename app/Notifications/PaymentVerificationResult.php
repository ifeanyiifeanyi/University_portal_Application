<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class PaymentVerificationResult extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;
    protected $status;

    /**
     * Create a new notification instance.
     */
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
    public function toMail(object $notifiable): ?MailMessage
    {
        try {
            $amount = '₦' . number_format($this->payment->base_amount, 2);
            $date = now()->format('d M, Y h:i A');
            $status = ucfirst($this->status);

            // Super Admin notification
            if ($notifiable->user_type === 1 && optional($notifiable->admin)->role === 'superAdmin') {
                return $this->buildAdminMail($amount, $date, $status);
            }

            // Student notification
            if ($notifiable->user_type === 3) {
                return $this->buildStudentMail($notifiable, $amount, $date, $status);
            }

            // Default case: return null for unsupported user types
            Log::info('Notification skipped for unsupported user type', [
                'user_type' => $notifiable->user_type
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('Error building mail notification', [
                'error' => $e->getMessage(),
                'user_id' => $notifiable->id ?? 'unknown'
            ]);
            return null;
        }
    }

    /**
     * Build admin-specific mail message
     */
    protected function buildAdminMail(string $amount, string $date, string $status): MailMessage
    {
        return (new MailMessage)
            ->subject('Manual Payment Verification Completed')
            ->greeting('Hello ADMIN,')
            ->line("The manual payment submitted by {$this->payment->student->full_name} has been {$this->status}.")
            ->line("Transaction Reference: {$this->payment->transaction_reference}")
            ->line("Amount: {$amount}")
            ->line("Verification Status: {$status}")
            ->line("Date Verified: {$date}")
            ->action('View Payment Details', url("/admin/payments/{$this->payment->id}"))
            ->line('Thank you for your diligence.');
    }

    /**
     * Build student-specific mail message
     */
    protected function buildStudentMail(object $notifiable, string $amount, string $date, string $status): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Verification Status')
            ->greeting("Hello {$notifiable->full_name},")
            ->line("Your manual payment has been {$this->status}.")
            ->line("Transaction Reference: {$this->payment->transaction_reference}")
            ->line("Amount: {$amount}")
            ->line("Verification Status: {$status}")
            ->line("Date Verified: {$date}")
            ->action('View Payment Details', url("/student/payments/{$this->payment->id}"))
            ->line('Thank you for your patience.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'transaction_reference' => $this->payment->transaction_reference,
            'amount' => $this->payment->base_amount,
            'status' => $this->status,
            'verified_at' => now()->format('d M, Y h:i A'),
            'payment_id' => $this->payment->id
        ];
    }
}
