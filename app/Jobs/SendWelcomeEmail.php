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

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $student;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Student $student
     */
    public function __construct(User $user, Student $student)
    {
        $this->user = $user;
        $this->student = $student;
    }


    /**
     * Execute the job.
     */
    public function handle()
    {
        // Send welcome email to the student
        Mail::to($this->user->email)->send(new WelcomeStudentMail($this->user, $this->student));
    }
}
