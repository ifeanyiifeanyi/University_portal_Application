<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessStudentImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $filePath;
    protected $departmentId;
    /**
     * Create a new job instance.
     */
    public function __construct($filePath, $departmentId)
    {
        $this->filePath = $filePath;
        $this->departmentId = $departmentId;
    }

    /**
     * Execute the job.
     */

    public function handle()
    {
        Excel::import(new StudentsImport($this->departmentId), storage_path('app/' . $this->filePath));
        unlink(storage_path('app/' . $this->filePath)); // Clean up

    }
}
