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
            // [
            //     'code' => 'CS101',
            //     'title' => 'Introduction to Computer Science',
            //     'description' => 'An introductory course to computer science principles.',
            //     'credit_hours' => 3,
            // ],
            // [
            //     'code' => 'MATH201',
            //     'title' => 'Calculus I',
            //     'description' => 'Fundamental concepts of single-variable calculus.',
            //     'credit_hours' => 4,
            // ],
            [
                'code' => 'PHYS101',
                'title' => 'General Physics I',
                'description' => 'Introduction to classical mechanics and thermodynamics.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'ENG101',
                'title' => 'English Composition',
                'description' => 'Development of written communication skills.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'HIST101',
                'title' => 'World History',
                'description' => 'Survey of world history from ancient to modern times.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'BIO101',
                'title' => 'General Biology I',
                'description' => 'Introduction to the principles of biology.',
                'credit_hours' => 4,
            ],
            [
                'code' => 'CHEM101',
                'title' => 'General Chemistry I',
                'description' => 'Introduction to chemical principles and laboratory techniques.',
                'credit_hours' => 4,
            ],
            [
                'code' => 'CS201',
                'title' => 'Data Structures and Algorithms',
                'description' => 'Study of data structures and algorithms for efficient problem-solving.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'MATH202',
                'title' => 'Calculus II',
                'description' => 'Continuation of single-variable calculus, including series and integration techniques.',
                'credit_hours' => 4,
            ],
            [
                'code' => 'PHYS102',
                'title' => 'General Physics II',
                'description' => 'Continuation of classical mechanics and introduction to electromagnetism.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'CS301',
                'title' => 'Operating Systems',
                'description' => 'Study of operating system principles, including process management and memory management.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'MATH301',
                'title' => 'Linear Algebra',
                'description' => 'Introduction to vector spaces, linear transformations, and matrices.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'STAT201',
                'title' => 'Introduction to Statistics',
                'description' => 'Fundamental concepts of statistical analysis and data interpretation.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'CS401',
                'title' => 'Software Engineering',
                'description' => 'Principles and practices of software development and project management.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'ENG201',
                'title' => 'Technical Writing',
                'description' => 'Development of writing skills for technical and scientific communication.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'CIS101',
                'title' => 'Introduction to Information Systems',
                'description' => 'Overview of information systems and their role in organizations.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'CIS102',
                'title' => 'Programming Fundamentals',
                'description' => 'Introduction to programming concepts and techniques.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'CIS201',
                'title' => 'Database Management Systems',
                'description' => 'Study of database design, implementation, and management.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'CIS202',
                'title' => 'Web Development',
                'description' => 'Introduction to web development technologies and practices.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'CIS301',
                'title' => 'Systems Analysis and Design',
                'description' => 'Techniques for analyzing and designing information systems.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'CIS302',
                'title' => 'Network and Communications',
                'description' => 'Fundamentals of computer networking and data communications.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'CIS303',
                'title' => 'Information Security',
                'description' => 'Principles and practices of securing information systems.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'CIS304',
                'title' => 'Mobile Application Development',
                'description' => 'Techniques for developing applications for mobile devices.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'CIS401',
                'title' => 'Advanced Database Systems',
                'description' => 'Advanced topics in database systems and applications.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'CIS402',
                'title' => 'Cloud Computing',
                'description' => 'Introduction to cloud computing technologies and services.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'CIS403',
                'title' => 'Big Data Analytics',
                'description' => 'Techniques and tools for analyzing large datasets.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'CIS404',
                'title' => 'Artificial Intelligence',
                'description' => 'Introduction to artificial intelligence and its applications.',
                'credit_hours' => 3,
            ],
            [
                'code' => 'CIS405',
                'title' => 'Capstone Project',
                'description' => 'A project-based course to apply knowledge and skills in a real-world setting.',
                'credit_hours' => 3,
            ]
            // Add more courses as needed
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
