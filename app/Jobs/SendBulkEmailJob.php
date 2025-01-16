<?php

namespace App\Jobs;

use App\Models\StudentEmail;
use Illuminate\Bus\Queueable;
use App\Mail\StudentCustomEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $students;
    protected $subject;
    protected $message;
    protected $attachments;
    protected $admin_id;

    public function __construct($students, $subject, $message, $attachments, $admin_id)
    {
        $this->students = $students;
        $this->subject = $subject;
        $this->message = $message;
        $this->attachments = $attachments;
        $this->admin_id = $admin_id;
    }

    public function handle()
    {
        foreach ($this->students as $student) {
            // Store attachments
            $storedAttachments = [];
            foreach ($this->attachments as $attachment) {
                $path = Storage::disk('public')->putFile(
                    'email-attachments/' . $student->id,
                    $attachment
                );
                $storedAttachments[] = $path;
            }

            // Create email record
            $studentEmail = StudentEmail::create([
                'student_id' => $student->id,
                'sender_id' => $this->admin_id,
                'admin_id' => $this->admin_id,
                'subject' => $this->subject,
                'message' => $this->message,
                'type' => 'bulk',
                'attachments' => $storedAttachments,
            ]);

            // Send email
            try {
                Mail::to($student->user->email)
                    ->send(new StudentCustomEmail(
                        $student,
                        $this->subject,
                        $this->message,
                        $storedAttachments,
                        $studentEmail
                    ));

                // $studentEmail->update(['status' => 'sent']);
                $studentEmail->status = 'sent';
            } catch (\Exception $e) {
                // $studentEmail->update([
                //     'status' => 'failed',
                //     'error_message' => $e->getMessage()
                // ]);
                $studentEmail->status = 'failed';
                $studentEmail->error_message = $e->getMessage();
            }
            $studentEmail->save();
        }
    }
}
