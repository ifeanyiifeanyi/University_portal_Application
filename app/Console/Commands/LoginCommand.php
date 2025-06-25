<?php

namespace App\Console\Commands;

use Telegram\Bot\Commands\Command;
use App\Models\TelegramAuth;
use App\Models\User;
use Illuminate\Support\Str;

class LoginCommand extends Command
{
    protected String $name = 'login';
    protected String $description = 'Connect your student account';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $telegramId = $this->update->getMessage()->getFrom()->getId();

        // Check if already logged in
        $auth = TelegramAuth::where('telegram_id', $telegramId)
            ->where('is_active', true)
            ->first();

        if ($auth) {
            $this->replyWithMessage([
                'text' => "You're already logged in as {$auth->user->name}! 🔓\n" .
                    "You can use /results, /materials, or /timetable.",
            ]);
            return;
        }

        // Generate unique token for login
        $token = Str::random(32);

        // Store token for this Telegram ID
        TelegramAuth::create([
            'telegram_id' => $telegramId,
            'token' => $token,
            'expires_at' => now()->addMinutes(30), // Token expires in 30 minutes
            'is_active' => false,
        ]);

        // Generate login URL
        $loginUrl = route('telegram.auth', ['token' => $token]);

        $this->replyWithMessage([
            'text' => "To connect your student account, please click the link below and login with your university credentials:\n\n" .
                $loginUrl . "\n\n" .
                "This link will expire in 30 minutes for security reasons.",
            'disable_web_page_preview' => true,
        ]);
    }
}
