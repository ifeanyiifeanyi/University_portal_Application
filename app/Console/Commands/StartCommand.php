<?php

namespace App\Console\Commands;

use Telegram\Bot\Commands\Command;


class StartCommand extends Command
{

    protected String $name = 'start';
    protected String $description = 'Start the bot and get help info';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->replyWithMessage([
            'text' => "Welcome to University Management Bot! 🎓\n\n" .
                "I can help you access your student dashboard. Here are available commands:\n" .
                "/login - Connect your student account\n" .
                "/results - View your semester results\n" .
                "/materials - Access course materials\n" .
                "/timetable - Check your class schedule\n" .
                "/logout - Disconnect your account\n" .
                "/help - Show this help message",
        ]);
    }
}
