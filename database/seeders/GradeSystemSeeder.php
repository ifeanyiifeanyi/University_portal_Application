<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GradeSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     */
    public function run(): void
    {
        DB::table('grade_systems')->insert([
            ['grade' => 'A', 'min_score' => 70.00, 'max_score' => 100.00],
            ['grade' => 'B', 'min_score' => 60.00, 'max_score' => 69.99],
            ['grade' => 'C', 'min_score' => 50.00, 'max_score' => 59.99],
            ['grade' => 'D', 'min_score' => 45.00, 'max_score' => 49.99],
            ['grade' => 'F', 'min_score' => 0.00, 'max_score' => 44.99],
        ]);
    }
}
