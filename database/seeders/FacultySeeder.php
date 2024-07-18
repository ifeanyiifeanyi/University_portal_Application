<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faculties = [
            [
                'code' => 'SCI',
                'name' => 'Faculty of Science',
                'description' => 'Dedicated to scientific research and education.',
            ],
            [
                'code' => 'ENG',
                'name' => 'Faculty of Engineering',
                'description' => 'Focused on engineering disciplines and innovation.',
            ],
            // Add more faculties as needed
        ];

        foreach ($faculties as $faculty) {
            Faculty::create($faculty);
        }
    }
}
