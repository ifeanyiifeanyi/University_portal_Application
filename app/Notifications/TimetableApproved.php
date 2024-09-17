<?php

namespace App\Notifications;

use App\Models\TimeTable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TimetableApproved extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $timetable;
    public function __construct(TimeTable $timetable)
    {
        $this->timetable = $timetable;
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
        return (new MailMessage)
            ->line('The timetable has been approved.')
            ->action('View Timetable', url('/timetable/' . $this->timetable->id))
            ->line('Please review the timetable for your upcoming classes.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'The timetable has been approved.',
            'timetable_id' => $this->timetable->id,
        ];
    }
}
