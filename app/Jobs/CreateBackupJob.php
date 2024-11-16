<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    public $timeout = 3600; // 1 hour
    public $tries = 1;

    public function __construct($type = 'full')
    {
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            ini_set('memory_limit', '512M');

            switch ($this->type) {
                case 'files':
                    Artisan::call('backup:run', ['--only-files' => true]);
                    break;
                case 'db':
                    Artisan::call('backup:run', ['--only-db' => true]);
                    break;
                default:
                    Artisan::call('backup:run');
                    break;
            }

            Log::info('Backup completed successfully: ' . $this->type);
        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
