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
            ['code' => 'CS101', 'title' => 'Introduction to Computer Science', 'description' => 'An introductory course to computer science principles.', 'credit_hours' => 3],
            ['code' => 'MATH201', 'title' => 'Calculus I', 'description' => 'Fundamental concepts of single-variable calculus.', 'credit_hours' => 4],
            ['code' => 'PHY101', 'title' => 'Physics I', 'description' => 'Introduction to basic physics principles.', 'credit_hours' => 3],
            ['code' => 'CHEM101', 'title' => 'General Chemistry', 'description' => 'Basic concepts of chemistry.', 'credit_hours' => 3],
            ['code' => 'BIO101', 'title' => 'Biology I', 'description' => 'Introduction to biological principles.', 'credit_hours' => 3],
            ['code' => 'ENG101', 'title' => 'English Composition', 'description' => 'Basics of writing and composition.', 'credit_hours' => 3],
            ['code' => 'HIST101', 'title' => 'World History', 'description' => 'A survey of world history.', 'credit_hours' => 3],
            ['code' => 'PSY101', 'title' => 'Introduction to Psychology', 'description' => 'Basic principles of psychology.', 'credit_hours' => 3],
            ['code' => 'SOC101', 'title' => 'Sociology', 'description' => 'Introduction to sociology.', 'credit_hours' => 3],
            ['code' => 'ECON101', 'title' => 'Principles of Economics', 'description' => 'Introduction to economics.', 'credit_hours' => 3],
            ['code' => 'CS201', 'title' => 'Data Structures and Algorithms', 'description' => 'Advanced concepts in data structures and algorithms.', 'credit_hours' => 3],
            ['code' => 'MATH301', 'title' => 'Linear Algebra', 'description' => 'Introduction to linear algebra.', 'credit_hours' => 3],
            ['code' => 'PHY201', 'title' => 'Physics II', 'description' => 'Continuation of Physics I.', 'credit_hours' => 3],
            ['code' => 'CHEM201', 'title' => 'Organic Chemistry', 'description' => 'Introduction to organic chemistry.', 'credit_hours' => 4],
            ['code' => 'BIO201', 'title' => 'Biology II', 'description' => 'Continuation of Biology I.', 'credit_hours' => 3],
            ['code' => 'ENG201', 'title' => 'Advanced Composition', 'description' => 'Advanced writing and composition.', 'credit_hours' => 3],
            ['code' => 'HIST201', 'title' => 'American History', 'description' => 'A survey of American history.', 'credit_hours' => 3],
            ['code' => 'PSY201', 'title' => 'Developmental Psychology', 'description' => 'Principles of human development.', 'credit_hours' => 3],
            ['code' => 'SOC201', 'title' => 'Cultural Sociology', 'description' => 'Study of cultures in sociology.', 'credit_hours' => 3],
            ['code' => 'ECON201', 'title' => 'Microeconomics', 'description' => 'Advanced principles of microeconomics.', 'credit_hours' => 3],
        ];


        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
