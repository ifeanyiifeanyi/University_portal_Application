<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $courses = [
            [
                'code' => 'CS101',
                'title' => 'Introduction to Computer Science',
                'description' => 'An introductory course to computer science principles.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'MATH201',
                'title' => 'Calculus I',
                'description' => 'Fundamental concepts of single-variable calculus.',
                'credit_hours' => 4,
            ],
            // Add more courses as needed
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
