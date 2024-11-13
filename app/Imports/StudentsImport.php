<?php

namespace App\Imports;

use Exception;
use App\Services\StudentBatchService;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class StudentsImport implements ToArray, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    private $department_id;
    private $studentBatchService;
    private $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => []
    ];

    public function __construct($department_id)
    {
        $this->department_id = $department_id;
        $this->studentBatchService = new StudentBatchService();
    }

    public function array(array $rows)
    {
        try {
            Log::info('Processing Excel import', [
                'department_id' => $this->department_id,
                'row_count' => count($rows)
            ]);

            $batchResults = $this->studentBatchService->processStudents($rows, $this->department_id);

            // Aggregate results
            $this->results['success'] += $batchResults['success'];
            $this->results['failed'] += $batchResults['failed'];
            $this->results['errors'] = array_merge($this->results['errors'], $batchResults['errors']);

        } catch (Exception $e) {
            Log::error('Excel import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getResults()
    {
        return $this->results;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 50;
    }
}
