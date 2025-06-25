<?php

namespace App\Console\Commands;

use Telegram\Bot\Commands\Command;

class HelpCommand extends Command
{
   protected string $name = 'help';
   protected string $description = 'Get help with using the bot';

    public function handle()
    {
        $this->replyWithMessage([
            'text' => "University Management Bot Help 🎓\n\n" .
                "Available commands:\n" .
                "/login - Connect your student account\n" .
                "/results - View your semester results\n" .
                "/materials - Access course materials\n" .
                "/timetable - Check your class schedule\n" .
                "/logout - Disconnect your account\n" .
                "/help - Show this help message\n\n" .
                "Need further assistance? Contact university IT support."
        ]);
    }
}
