<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Jobs\CreateBackupJob;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupSettingController extends Controller
{
    private $disk;
    private $backupDirectory = 'college_of_nursing';
    private $startTime;

    public function __construct()
    {
        $this->disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $this->startTime = now();
    }

    // Centralized logging methods
    private function logBackupActivity($action, $filename, $type = null, $additionalProperties = [])
    {
        $baseProperties = [
            'filename' => $filename,
            'start_time' => $this->startTime->toDateTimeString(),
            'end_time' => now()->toDateTimeString(),
            'disk' => config('backup.backup.destination.disks')[0],
            'directory' => $this->backupDirectory,
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];

        if ($type) {
            $baseProperties['backup_type'] = $type;
        }

        if (file_exists(storage_path("app/{$this->backupDirectory}/" . $filename))) {
            $baseProperties['file_size'] = $this->formatFileSize(
                filesize(storage_path("app/{$this->backupDirectory}/" . $filename))
            );
        }

        $properties = array_merge($baseProperties, $additionalProperties);

        activity()
            ->causedBy(auth()->user())
            ->withProperties($properties)
            ->log($action);
    }

    private function logErrorActivity($action, $exception, $additionalProperties = [])
    {
        $errorProperties = [
            'error' => $exception->getMessage(),
            'error_code' => $exception->getCode(),
            'error_file' => $exception->getFile(),
            'error_line' => $exception->getLine(),
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ];

        $properties = array_merge($errorProperties, $additionalProperties);

        activity()
            ->causedBy(auth()->user())
            ->withProperties($properties)
            ->log($action);

        Log::error("$action: " . $exception->getMessage());
    }

    private function getBackupType($fileName)
    {
        if (strpos($fileName, '-db-') !== false) {
            return 'Database Only';
        } elseif (strpos($fileName, '-files-') !== false) {
            return 'Files Only';
        }
        return 'Full Backup';
    }


    // public function index()
    // {
    //     $files = $this->disk->files($this->backupDirectory);

    //     $backups = [];
    //     foreach ($files as $file) {
    //         if (substr($file, -4) == '.zip') {
    //             $fileName = basename($file);
    //             $backups[] = [
    //                 'file_name' => $fileName,
    //                 'file_size' => $this->formatFileSize($this->disk->size($file)),
    //                 'created_at' => $this->disk->lastModified($file),
    //                 'download_link' => route('admin.backups.download', ['file_name' => $fileName]),
    //                 'backup_type' => $this->getBackupType($fileName)
    //             ];
    //         }
    //     }

    //     usort($backups, function ($a, $b) {
    //         return $b['created_at'] - $a['created_at'];
    //     });

    //     return view('admin.backupmanager.index', compact('backups'));
    // }

    public function index()
    {
        $files = $this->disk->files($this->backupDirectory);

        $backups = [];
        // Get the latest backup type from the session if it exists
        $latestBackupType = session('latest_backup_type');
        $latestBackupFile = session('latest_backup_file');
        // dd($latestBackupType);

        foreach ($files as $file) {
            if (substr($file, -4) == '.zip') {
                $fileName = basename($file);
                $backupType = 'Full Backup'; // Default type

                // If this is the latest backup file, use the type from session
                if ($latestBackupFile === $fileName) {
                    $backupType = $latestBackupType;
                }

                $backups[] = [
                    'file_name' => $fileName,
                    'file_size' => $this->formatFileSize($this->disk->size($file)),
                    'created_at' => $this->disk->lastModified($file),
                    'download_link' => route('admin.backups.download', ['file_name' => $fileName]),
                    'backup_type' => $backupType
                ];
            }
        }

        // Sort backups by created_at in descending order
        usort($backups, function ($a, $b) {
            return $b['created_at'] - $a['created_at'];
        });

        return view('admin.backupmanager.index', compact('backups'));
    }

    // public function create()
    // {
    //     try {
    //         CreateBackupJob::dispatch('full');
    //         return $this->redirectWithSuccess('Full backup created successfully!');
    //     } catch (\Exception $e) {
    //         Log::error('Backup failed: ' . $e->getMessage());
    //         return $this->redirectWithError('Backup failed: ' . $e->getMessage());
    //     }
    // }

    public function create()
    {
        try {
            CreateBackupJob::dispatch('full');

            $filename = date('Y-m-d-His') . '.zip';
            session(['latest_backup_type' => 'Full Backup']);
            session(['latest_backup_file' => $filename]);

            $this->logBackupActivity(
                'Created a full backup with system and database files',
                $filename,
                'Full Backup'
            );

            return $this->redirectWithSuccess('Full backup created successfully!');
        } catch (\Exception $e) {
            $this->logErrorActivity(
                'Failed to create full backup',
                $e,
                ['backup_type' => 'Full Backup']
            );
            return $this->redirectWithError('Backup failed: ' . $e->getMessage());
        }
    }

    public function createFilesOnly()
    {
        try {
            CreateBackupJob::dispatch('files');

            $filename = date('Y-m-d-His') . '.zip';
            session(['latest_backup_type' => 'Files Only']);
            session(['latest_backup_file' => $filename]);

            $this->logBackupActivity(
                'Created a files-only backup',
                $filename,
                'Files Only'
            );

            return $this->redirectWithSuccess('Files backup created successfully!');
        } catch (\Exception $e) {
            $this->logErrorActivity(
                'Failed to create files backup',
                $e,
                ['backup_type' => 'Files Only']
            );
            return $this->redirectWithError('Files backup failed: ' . $e->getMessage());
        }
    }

    public function createDatabaseOnly()
    {
        try {
            CreateBackupJob::dispatch('db');

            $filename = date('Y-m-d-His') . '.zip';
            session(['latest_backup_type' => 'Database Only']);
            session(['latest_backup_file' => $filename]);

            $this->logBackupActivity(
                'Created a database-only backup',
                $filename,
                'Database Only',
                ['database_name' => config('database.connections.mysql.database')]
            );

            return $this->redirectWithSuccess('Database backup created successfully!');
        } catch (\Exception $e) {
            $this->logErrorActivity(
                'Failed to create database backup',
                $e,
                ['backup_type' => 'Database Only']
            );
            return $this->redirectWithError('Database backup failed: ' . $e->getMessage());
        }
    }
    // public function download($fileName)
    // {
    //     try {
    //         // Sanitize filename and ensure it only contains the base name
    //         $fileName = basename(strip_tags($fileName));

    //         // Construct the full backup path
    //         $backupPath = storage_path("app/{$this->backupDirectory}/" . $fileName);
    //         Log::info('Attempting to download backup file: ' . $backupPath);

    //         if (!file_exists($backupPath)) {
    //             Log::error('Backup file not found: ' . $backupPath);
    //             return $this->redirectWithError('Backup file not found.');
    //         }

    //         $fileSize = filesize($backupPath);
    //         Log::info('File size: ' . $fileSize . ' bytes');

    //         $headers = [
    //             'Content-Type' => 'application/zip',
    //             'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    //             'Content-Length' => $fileSize,
    //             'Cache-Control' => 'no-cache, no-store, must-revalidate',
    //             'Pragma' => 'no-cache',
    //             'Expires' => '0'
    //         ];

    //         Log::info('Sending file: ' . $fileName);
    //         return response()->download($backupPath, $fileName, $headers);
    //     } catch (\Exception $e) {
    //         Log::error('Backup download failed: ' . $e->getMessage());
    //         Log::error($e->getTraceAsString());
    //         return $this->redirectWithError('Download failed: ' . $e->getMessage());
    //     }
    // }

    public function download($fileName)
    {
        try {
            $fileName = basename(strip_tags($fileName));
            $backupPath = storage_path("app/{$this->backupDirectory}/" . $fileName);

            if (!file_exists($backupPath)) {
                $this->logBackupActivity(
                    'Failed to download backup - file not found',
                    $fileName,
                    null,
                    ['attempted_path' => $backupPath]
                );
                return $this->redirectWithError('Backup file not found.');
            }

            $this->logBackupActivity(
                'Downloaded backup file',
                $fileName
            );

            $fileSize = filesize($backupPath);
            $headers = [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Content-Length' => $fileSize,
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];

            return response()->download($backupPath, $fileName, $headers);
        } catch (\Exception $e) {
            $this->logErrorActivity(
                'Failed to download backup',
                $e,
                ['filename' => $fileName]
            );
            return $this->redirectWithError('Download failed: ' . $e->getMessage());
        }
    }

    public function restore($fileName)
    {
        try {
            $fileName = basename(strip_tags($fileName));
            $backupPath = storage_path("app/{$this->backupDirectory}/" . $fileName);

            if (!file_exists($backupPath)) {
                $this->logBackupActivity(
                    'Failed to restore backup - file not found',
                    $fileName,
                    null,
                    ['attempted_path' => $backupPath]
                );
                return $this->redirectWithError('Backup file not found!');
            }

            $zip = new \ZipArchive;
            if ($zip->open($backupPath) === TRUE) {
                $zip->extractTo(storage_path('tmp/restore'));
                $zip->close();

                // Restore database
                $sqlFile = storage_path('tmp/restore/db-dumps/mysql-*.sql');
                $files = glob($sqlFile);

                if (!empty($files)) {
                    $sql = file_get_contents($files[0]);
                    DB::unprepared($sql);
                }

                File::deleteDirectory(storage_path('tmp/restore'));

                $this->logBackupActivity(
                    'Successfully restored backup',
                    $fileName,
                    null,
                    ['restore_path' => storage_path('tmp/restore')]
                );

                return $this->redirectWithSuccess('Backup restored successfully!');
            }

            $this->logBackupActivity(
                'Failed to restore backup - could not open file',
                $fileName
            );
            return $this->redirectWithError('Could not open backup file.');
        } catch (\Exception $e) {
            $this->logErrorActivity(
                'Failed to restore backup',
                $e,
                ['filename' => $fileName]
            );
            return $this->redirectWithError('Restore failed: ' . $e->getMessage());
        }
    }

    public function delete($fileName)
    {
        try {
            $fileName = basename(strip_tags($fileName));
            $filePath = $this->backupDirectory . '/' . $fileName;

            if (!$this->disk->exists($filePath)) {
                $this->logBackupActivity(
                    'Failed to delete backup - file not found',
                    $fileName,
                    null,
                    ['attempted_path' => $filePath]
                );
                return $this->redirectWithError('Backup file not found!');
            }

            $this->disk->delete($filePath);

            $this->logBackupActivity(
                'Deleted backup file',
                $fileName,
                null,
                ['file_path' => $filePath]
            );

            return $this->redirectWithSuccess('Backup deleted successfully!');
        } catch (\Exception $e) {
            $this->logErrorActivity(
                'Failed to delete backup',
                $e,
                ['filename' => $fileName]
            );
            return $this->redirectWithError('Delete failed: ' . $e->getMessage());
        }
    }
    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    private function redirectWithSuccess($message)
    {
        return redirect()->route('admin.backups.index')->with('success', $message);
    }

    private function redirectWithError($message)
    {
        return redirect()->route('admin.backups.index')->with('error', $message);
    }
}
