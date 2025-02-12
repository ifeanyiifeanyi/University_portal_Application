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


    public function index()
    {
        $files = $this->disk->files($this->backupDirectory);
        $backups = [];

        // Get the backup types from session
        $backupTypes = session('backup_types', []);

        foreach ($files as $file) {
            if (substr($file, -4) == '.zip') {
                $fileName = basename($file);

                // Get backup type from session storage if exists, otherwise detect from filename
                $backupType = isset($backupTypes[$fileName])
                    ? $backupTypes[$fileName]
                    : $this->detectBackupType($fileName);

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

        // dd($backups);

        return view('admin.backupmanager.index', compact('backups'));
    }


    // public function createUrgent($type)
    // {
    //     try {
    //         CreateBackupJob::dispatch($type)->onQueue('urgent-backups');
    //         return redirect()->route('admin.backups.index')
    //             ->with('success', ucfirst($type) . ' backup initiated successfully!');
    //     } catch (\Exception $e) {
    //         return redirect()->route('admin.backups.index')
    //             ->with('error', 'Backup failed: ' . $e->getMessage());
    //     }
    // }

    public function createUrgent($type)
    {
        try {
            // Validate backup type
            if (!in_array($type, ['full', 'files', 'db'])) {
                return $this->redirectWithError('Invalid backup type specified.');
            }

            // Create a unique identifier for this backup
            $backupId = uniqid('backup_');

            // Store in session that we're expecting this backup
            session(['pending_backup_' . $backupId => [
                'type' => $type,
                'started_at' => now(),
                'status' => 'pending'
            ]]);

            // Dispatch job to urgent queue
            CreateBackupJob::dispatch($type, $backupId)
                ->onQueue('urgent-backups');

            // Log the initiation
            $this->logBackupActivity(
                'Urgent backup initiated',
                date('Y-m-d-His') . '-' . $type . '.zip',
                ucfirst($type),
                ['backup_id' => $backupId]
            );

            return $this->redirectWithSuccess('Urgent ' . ucfirst($type) . ' backup initiated. You will receive an email when it\'s complete.');
        } catch (\Exception $e) {
            $this->logErrorActivity(
                'Failed to initiate urgent backup',
                $e,
                ['backup_type' => $type]
            );
            return $this->redirectWithError('Failed to initiate backup: ' . $e->getMessage());
        }
    }

     // Add this method to check backup status
     public function checkBackupStatus($backupId)
     {
         $status = session('pending_backup_' . $backupId);
         return response()->json($status);
     }


    public function download($fileName)
    {
        try {
            $fileName = basename(strip_tags($fileName));
            $backupPath = storage_path("app/{$this->backupDirectory}/" . $fileName);

            if (!file_exists($backupPath)) {
                return redirect()->route('admin.backups.index')
                    ->with('error', 'Backup file not found.');
            }

            return response()->download($backupPath, $fileName, [
                'Content-Type' => 'application/zip',
                'Content-Length' => filesize($backupPath),
                'Cache-Control' => 'no-cache'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.backups.index')
                ->with('error', 'Download failed: ' . $e->getMessage());
        }
    }

    private function detectBackupType($fileName)
    {
        if (strpos($fileName, '-db-') !== false) return 'Database Only';
        if (strpos($fileName, '-files-') !== false) return 'Files Only';
        return 'Full Backup';
    }



    private function storeBackupType($fileName, $type)
    {
        // Get current backup types from session
        $backupTypes = session('backup_types', []);

        // Add new backup type
        $backupTypes[$fileName] = $type;

        // Store back in session
        session(['backup_types' => $backupTypes]);
    }


    public function create()
    {
        // dd("we got here");
        try {
            CreateBackupJob::dispatch('full');

            $filename = date('Y-m-d-His') . '.zip';
            $this->storeBackupType($filename, 'Full Backup');

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
            $this->storeBackupType($filename, 'Files Only');

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
            $this->storeBackupType($filename, 'Database Only');

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



    public function restore($fileName)
    {
        try {
            $fileName = basename(strip_tags($fileName));
            $backupPath = storage_path("app/{$this->backupDirectory}/" . $fileName);
            $backupType = $this->getBackupTypeFromSession($fileName);

            if (!file_exists($backupPath)) {
                $this->logBackupActivity(
                    'Failed to restore backup - file not found',
                    $fileName,
                    $backupType,
                    ['attempted_path' => $backupPath]
                );
                return $this->redirectWithError('Backup file not found!');
            }

            // Validate zip file before restoration
            if (!$this->isValidBackupZip($backupPath, $backupType)) {
                $this->logBackupActivity(
                    'Failed to restore backup - invalid backup file',
                    $fileName,
                    $backupType,
                    ['validation_error' => 'Backup file structure does not match expected type']
                );
                return $this->redirectWithError('Invalid backup file structure!');
            }

            $zip = new \ZipArchive;
            if ($zip->open($backupPath) === TRUE) {
                $tempRestorePath = storage_path('tmp/restore');

                // Clean up any existing temp files
                if (File::exists($tempRestorePath)) {
                    File::deleteDirectory($tempRestorePath);
                }

                // Create temp directory
                File::makeDirectory($tempRestorePath, 0755, true);

                // Extract files
                $zip->extractTo($tempRestorePath);
                $zip->close();

                // Perform restoration based on backup type
                $restored = $this->performRestore($tempRestorePath, $backupType);

                // Clean up
                File::deleteDirectory($tempRestorePath);

                if (!$restored) {
                    throw new \Exception('Restoration process failed');
                }

                $this->logBackupActivity(
                    'Successfully restored backup',
                    $fileName,
                    $backupType,
                    [
                        'restore_path' => $tempRestorePath,
                        'restored_type' => $backupType
                    ]
                );

                return $this->redirectWithSuccess('Backup restored successfully!');
            }

            $this->logBackupActivity(
                'Failed to restore backup - could not open file',
                $fileName,
                $backupType
            );
            return $this->redirectWithError('Could not open backup file.');
        } catch (\Exception $e) {
            $this->logErrorActivity(
                'Failed to restore backup',
                $e,
                [
                    'filename' => $fileName,
                    'backup_type' => $backupType
                ]
            );
            return $this->redirectWithError('Restore failed: ' . $e->getMessage());
        }
    }

    private function getBackupTypeFromSession($fileName)
    {
        $backupTypes = session('backup_types', []);
        return $backupTypes[$fileName] ?? $this->detectBackupType($fileName);
    }

    private function isValidBackupZip($backupPath, $backupType)
    {
        try {
            $zip = new \ZipArchive;
            if ($zip->open($backupPath) !== TRUE) {
                return false;
            }

            // Check file structure based on backup type
            switch ($backupType) {
                case 'Database Only':
                    $hasDbFile = false;
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        if (strpos($zip->getNameIndex($i), 'db-dumps/mysql-') !== false) {
                            $hasDbFile = true;
                            break;
                        }
                    }
                    $zip->close();
                    return $hasDbFile;

                case 'Files Only':
                    $hasFiles = false;
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        if (strpos($zip->getNameIndex($i), 'files/') !== false) {
                            $hasFiles = true;
                            break;
                        }
                    }
                    $zip->close();
                    return $hasFiles;

                case 'Full Backup':
                    $hasDbFile = false;
                    $hasFiles = false;
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $fileName = $zip->getNameIndex($i);
                        if (strpos($fileName, 'db-dumps/mysql-') !== false) {
                            $hasDbFile = true;
                        }
                        if (strpos($fileName, 'files/') !== false) {
                            $hasFiles = true;
                        }
                    }
                    $zip->close();
                    return $hasDbFile && $hasFiles;

                default:
                    $zip->close();
                    return false;
            }
        } catch (\Exception $e) {
            Log::error('Backup validation failed: ' . $e->getMessage());
            return false;
        }
    }

    private function performRestore($tempRestorePath, $backupType)
    {
        try {
            switch ($backupType) {
                case 'Database Only':
                case 'Full Backup':
                    // Restore database if it's a DB or full backup
                    $sqlFile = $tempRestorePath . '/db-dumps/mysql-*.sql';
                    $files = glob($sqlFile);
                    if (!empty($files)) {
                        $sql = file_get_contents($files[0]);
                        DB::unprepared($sql);
                    }

                    if ($backupType === 'Database Only') {
                        break;
                    }
                    // Fall through for full backup to also restore files

                case 'Files Only':
                    // Restore files if it's a files or full backup
                    $filesPath = $tempRestorePath . '/files';
                    if (File::exists($filesPath)) {
                        // Add your file restoration logic here
                        // Be careful to handle permissions and existing files
                    }
                    break;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Restore process failed: ' . $e->getMessage());
            return false;
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

            // Remove the backup type from session when file is deleted
            $backupTypes = session('backup_types', []);
            unset($backupTypes[$fileName]);
            session(['backup_types' => $backupTypes]);

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
