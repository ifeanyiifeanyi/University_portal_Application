<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use ZipArchive;
use Carbon\Carbon;


class ArchiveLogActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:archive-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive activity logs older than 30 days';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting activity log archival process...');

        try {
            // Get activities older than 30 days
            $cutoffDate = now()->subDays(30);
            $oldActivities = Activity::where('created_at', '<', $cutoffDate)
                ->with('causer')
                ->get();

            if ($oldActivities->isEmpty()) {
                $this->info('No activities to archive.');
                return;
            }

            $archiveDate = now()->format('Y_m');
            $csvFilename = "activity_log_archive_{$archiveDate}.csv";
            $zipFilename = "activity_log_archive_{$archiveDate}.zip";

            // Ensure storage directories exist
            Storage::makeDirectory('temp');
            Storage::makeDirectory('archives');

            // Create CSV
            $csvPath = Storage::path("temp/{$csvFilename}");
            $this->createCsvFile($csvPath, $oldActivities);

            // Create ZIP
            $zipPath = Storage::path("archives/{$zipFilename}");
            $this->createZipFile($zipPath, $csvPath, $csvFilename);

            // Clean up
            Storage::delete("temp/{$csvFilename}");

            // Delete archived records
            $deletedCount = Activity::where('created_at', '<', $cutoffDate)->delete();

            $this->info("Successfully archived {$deletedCount} records to {$zipFilename}");

        } catch (\Exception $e) {
            $this->error("Error during archival process: {$e->getMessage()}");
            report($e);
        }
    }

    private function createCsvFile(string $path, $activities): void
    {
        $file = fopen($path, 'w');

        // Headers based on Spatie Activity Log structure
        fputcsv($file, [
            'ID',
            'Log Name',
            'Description',
            'Subject Type',
            'Subject ID',
            'Causer Type',
            'Causer Name',
            'Properties',
            'Created At'
        ]);

        // Data
        foreach ($activities as $activity) {
            fputcsv($file, [
                $activity->id,
                $activity->log_name,
                $activity->description,
                $activity->subject_type,
                $activity->subject_id,
                $activity->causer_type,
                $activity->causer ? $activity->causer->name : 'System',
                json_encode($activity->properties),
                $activity->created_at->format('Y-m-d H:i:s')
            ]);
        }

        fclose($file);
    }

    private function createZipFile(string $zipPath, string $csvPath, string $csvFilename): void
    {
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $zip->addFile($csvPath, $csvFilename);
            $zip->close();
        } else {
            throw new \RuntimeException('Failed to create zip file');
        }
    }
}
