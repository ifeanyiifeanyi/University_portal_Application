<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ApprovedScoresExport implements FromCollection, WithHeadings
{
    private $approvedScores;

    public function __construct($approvedScores)
    {
        $this->approvedScores = $approvedScores;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->approvedScores;
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Department',
            'Course',
            'Teacher',
            'Assessment Score',
            'Exam Score',
            'Total Score',
            'Grade',
        ];
    }
}
