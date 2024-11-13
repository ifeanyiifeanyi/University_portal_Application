<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use App\Mail\WelcomeStudentMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendWelcomeEmail implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $student;
    protected $attempts;
    protected $maxAttempts;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Student $student
     * @param int $attempts
     * @param int $maxAttempts
     */
    public function __construct(User $user, Student $student, int $attempts = 1, int $maxAttempts = 5)
    {
        $this->user = $user;
        $this->student = $student;
        $this->attempts = $attempts;
        $this->maxAttempts = $maxAttempts;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Send welcome email to the student
        Mail::to($this->user->email)->send(new WelcomeStudentMail($this->user, $this->student));

        // If we haven't reached max attempts, dispatch another job with 1 second delay
        if ($this->attempts < $this->maxAttempts) {
            static::dispatch(
                $this->user,
                $this->student,
                $this->attempts + 1,
                $this->maxAttempts
            )->delay(now()->addSecond());
        }
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->user->id . '_' . $this->student->id . '_' . $this->attempts;
    }

    /**
     * Initial dispatch with configuration
     *
     * @param \App\Models\User $user
     * @param \App\Models\Student $student
     * @param int $totalEmails Number of times to send the email
     * @return void
     */
    public static function dispatchSequence(User $user, Student $student, int $totalEmails = 5)
    {
        static::dispatch($user, $student, 1, $totalEmails)->delay(now()->addSecond());
    }
}
