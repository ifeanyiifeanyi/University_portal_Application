<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use App\Imports\StudentsImport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\StudentBatchService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\ImportCompletedNotification;

class ProcessStudentImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $students;
    protected $departmentId;
    protected $batchId;

    public function __construct(array $students, $departmentId, string $batchId)
    {
        $this->students = $students;
        $this->departmentId = $departmentId;
        $this->batchId = $batchId;
    }

    public function handle()
    {
        $studentBatchService = new StudentBatchService();
        $results = $studentBatchService->processStudents($this->students, $this->departmentId);

        // Store results in cache with a unique key using the batch ID
        Cache::put("student_import_{$this->batchId}", $results, now()->addHours(24));
    }

}
