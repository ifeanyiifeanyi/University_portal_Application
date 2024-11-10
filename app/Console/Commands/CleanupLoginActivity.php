<?php

namespace App\Console\Commands;

use App\Models\LoginActivity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class CleanupLoginActivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'login:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old login activity records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = Config::get('login-security.activity_retention_days', 90);

        $deleted = LoginActivity::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("Deleted {$deleted} old login activity records.");
    }
}
