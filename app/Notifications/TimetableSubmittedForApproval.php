<?php

namespace App\Notifications;

use App\Models\TimeTable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TimetableSubmittedForApproval extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $timeTable;
    public function __construct(TimeTable $timeTable)
    {
        $this->timeTable = $timeTable;
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
            ->line('A new timetable has been submitted for approval.')
            ->action('Review Timetable', url('/admin/timetable/' . $this->timeTable->id))
            ->line('Thank you for your attention to this matter.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'A new timetable has been submitted for approval.',
            'timetable_id' => $this->timeTable->id,
        ];
    }
}
