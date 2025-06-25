<?php

namespace App\Console\Commands;

use Telegram\Bot\Commands\Command;
use App\Models\TelegramAuth;
use App\Models\GpaRecord as Result;

class ResultCommand extends Command
{
   protected string $name = 'results';
    protected string $description = 'View your semester results';

    public function handle()
    {
        $telegramId = $this->update->getMessage()->getFrom()->getId();

        // Check if logged in
        $auth = TelegramAuth::where('telegram_id', $telegramId)
            ->where('is_active', true)
            ->first();

        if (!$auth) {
            $this->replyWithMessage([
                'text' => "You need to login first! Use /login command to connect your student account."
            ]);
            return;
        }

        $user = $auth->user;

        // Check if the user is a student
        if ($user->role !== 'student') {
            $this->replyWithMessage([
                'text' => "Only students can view results. Your account is registered as {$user->role}."
            ]);
            return;
        }

        // Get student's latest results
        $results = Result::where('student_id', $user->student->id)
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('semester_id');

        if ($results->isEmpty()) {
            $this->replyWithMessage([
                'text' => "No results found for your account."
            ]);
            return;
        }

        // Format results by semester
        $response = "📊 *YOUR RESULTS* 📊\n\n";

        foreach ($results as $semester => $semesterResults) {
            $response .= "*SEMESTER {$semester}*\n";
            // $totalGradePoints = 0;
            // $totalCredits = 0;

            // foreach ($semesterResults as $result) {
            //     $response .= "{$result->course->code} - {$result->course->name}: {$result->grade} ({$result->score}%)\n";
            //     $totalGradePoints += $result->grade_point * $result->course->credit_hours;
            //     $totalCredits += $result->course->credit_hours;
            // }

            $gpa = $semesterResults->gpa ? round($semesterResults->gpa, 2) : 0;
            $response .= "Semester GPA: {$gpa}\n\n";
        }

        $this->replyWithMessage([
            'text' => $response,
            'parse_mode' => 'Markdown'
        ]);
    }
}
