<?php

namespace App\Jobs;

use App\Mail\BackupCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EmailBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    public $timeout = 3600;

    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $backupPath = storage_path('app/college_of_nursing');
        $latestBackup = collect(glob("{$backupPath}/*-{$this->type}-*.zip"))
            ->sortByDesc(fn($file) => filemtime($file))
            ->first();

        if ($latestBackup) {
            Mail::to(config('app.email'))
                ->send(new BackupCreated($latestBackup, $this->type));
        }
    }

    
}
