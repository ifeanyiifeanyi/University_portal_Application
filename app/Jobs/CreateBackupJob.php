<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Jobs\EmailBackupJob;

class CreateBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    // public $tries = 3;

    private string $type;
    private string $backupId;

    public function __construct(string $type = 'full', string $backupId = null)
    {
        $this->type = $type;
        $this->backupId = $backupId ?? uniqid('backup_');
    }

    public function handle()
    {
        try {
            Log::info("Starting backup job", ['type' => $this->type, 'id' => $this->backupId]);

            // Set status as processing
            Cache::put('backup_status_' . $this->backupId, 'processing', now()->addHours(1));

            ini_set('memory_limit', '512M');

            $command = match($this->type) {
                'files' => 'backup:run --only-files',
                'db' => 'backup:run --only-db',
                default => 'backup:run'
            };

            // Execute backup command
            $output = Artisan::call($command);

            if ($output !== 0) {
                throw new \Exception("Backup command failed with exit code: $output");
            }

            // Get the latest backup file
            $backupPath = storage_path('app/college_of_nursing');
            $pattern = "{$backupPath}/*-{$this->type}-*.zip";
            $latestBackup = collect(glob($pattern))
                ->sortByDesc(fn($file) => filemtime($file))
                ->first();

            if (!$latestBackup) {
                throw new \Exception('Backup file not found after creation');
            }

            // Update status as completed
            Cache::put('backup_status_' . $this->backupId, 'completed', now()->addHours(1));

            // Dispatch email job
            EmailBackupJob::dispatch($this->type, $latestBackup);

            Log::info('Backup completed successfully', [
                'type' => $this->type,
                'id' => $this->backupId,
                'file' => basename($latestBackup)
            ]);

        } catch (\Exception $e) {
            // Update status as failed
            Cache::put('backup_status_' . $this->backupId, 'failed', now()->addHours(1));

            Log::error('Backup failed', [
                'type' => $this->type,
                'id' => $this->backupId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }
}
