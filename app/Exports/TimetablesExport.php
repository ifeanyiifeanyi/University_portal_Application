<?php

namespace App\Exports;

use App\Models\TimeTable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class TimetablesExport implements FromCollection, WithHeadings
{

    protected $timetables;

    public function __construct($timetables)
    {
        $this->timetables = $timetables;
    }
    /**
     * @return \Illuminate\Support\Collection
     */


    public function collection()
    {
        return $this->timetables->map(function ($timetable) {
            return [
                'Academic Session' => $timetable->academicSession->name,
                'Semester' => $timetable->semester->name,
                'Department' => $timetable->department->name,
                'Level' => $timetable->level,
                'Day of Week' => TimeTable::getDayName($timetable->day_of_week),
                'Start Time' => $timetable->start_time->format('H:i'),
                'End Time' => $timetable->end_time->format('H:i'),
                'Course' => $timetable->course->code . ' - ' . $timetable->course->title,
                'Teacher' => $timetable->teacher->name,
                'Room' => $timetable->room,
                'Start Date' => $timetable->start_date->format('Y-m-d'),
                'End Date' => $timetable->end_date->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Academic Session',
            'Semester',
            'Department',
            'Level',
            'Day of Week',
            'Start Time',
            'End Time',
            'Course',
            'Teacher',
            'Room',
            'Start Date',
            'End Date',
        ];
    }
}
