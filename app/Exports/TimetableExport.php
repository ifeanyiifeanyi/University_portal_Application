<?php

namespace App\Exports;

use App\Models\TimeTable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TimetableExport implements FromCollection, WithHeadings
{
    protected $timetable;

    public function __construct(TimeTable $timetable)
    {
        $this->timetable = $timetable;
    }

    public function collection()
    {
        return collect([
            [
                'Day' => $this->timetable->day_of_week,
                'Start Time' => $this->timetable->start_time,
                'End Time' => $this->timetable->end_time,
                'Course' => $this->timetable->course->name,
                'Teacher' => $this->timetable->teacher->name,
                'Room' => $this->timetable->room,
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Day',
            'Start Time',
            'End Time',
            'Course',
            'Teacher',
            'Room',
        ];
    }
}
