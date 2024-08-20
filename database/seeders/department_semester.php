<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class department_semester extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dpsemesters = [
            [
                'department_id' => 1,
                'semester_id' => 1,
                'level' => 100,
                'max_credit_hours' => 24,
            ],
            [
                'department_id' => 2,
                'semester_id' => 1,
                'level' => 100,
                'max_credit_hours' => 24,
            ],
           
        ];

        foreach ($dpsemesters as $dpsemester) {
            DB::table('department_semester')->insert($dpsemester);
        }
    }
}
