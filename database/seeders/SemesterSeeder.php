<?php

namespace Database\Seeders;

use App\Models\Semester;
use App\Models\AcademicSession;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $academicSessions = AcademicSession::all();

        foreach ($academicSessions as $session) {
            Semester::create([
                'name' => 'Fall ' . substr($session->name, 0, 4),
                'season' => 'Fall',
                'start_date' => $session->start_date,
                'end_date' => date('Y-m-d', strtotime($session->start_date . ' +4 months')),
                'is_current' => $session->is_current,
                'academic_session_id' => $session->id,
            ]);

            Semester::create([
                'name' => 'Spring ' . substr($session->name, 5, 4),
                'season' => 'Spring',
                'start_date' => date('Y-m-d', strtotime($session->start_date . ' +4 months')),
                'end_date' => $session->end_date,
                'is_current' => false,
                'academic_session_id' => $session->id,
            ]);
        }
    }

}
