<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // First, let's get all faculties
        $faculties = Faculty::all()->keyBy('code');

        $departments = [
            [
                'code' => 'CS',
                'name' => 'Computer Science',
                'faculty_id' => $faculties['SCI']->id,
                'description' => 'Focuses on computer science and software engineering.',
            ],
            [
                'code' => 'MECH',
                'name' => 'Mechanical Engineering',
                'faculty_id' => $faculties['ENG']->id,
                'description' => 'Deals with design, manufacturing, and maintenance of mechanical systems.',
            ],
            // Add more departments as needed
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
