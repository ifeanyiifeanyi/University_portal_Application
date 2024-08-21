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
            ['code' => 'CS', 'name' => 'Computer Science', 'faculty_id' => $faculties['SCI']->id, 'description' => 'Focuses on computer science and software engineering.', 'duration' => 4],
            ['code' => 'MECH', 'name' => 'Mechanical Engineering', 'faculty_id' => $faculties['ENG']->id, 'description' => 'Deals with design, manufacturing, and maintenance of mechanical systems.', 'duration' => 5],
            ['code' => 'EE', 'name' => 'Electrical Engineering', 'faculty_id' => $faculties['ENG']->id, 'description' => 'Focuses on electrical systems and circuits.', 'duration' => 5],
            ['code' => 'CIV', 'name' => 'Civil Engineering', 'faculty_id' => $faculties['ENG']->id, 'description' => 'Studies in civil infrastructure and construction.', 'duration' => 5],
            ['code' => 'BIO', 'name' => 'Biology', 'faculty_id' => $faculties['SCI']->id, 'description' => 'Focuses on biological sciences.', 'duration' => 4],
            ['code' => 'CHEM', 'name' => 'Chemistry', 'faculty_id' => $faculties['SCI']->id, 'description' => 'Studies in chemical sciences.', 'duration' => 4],
            ['code' => 'PHY', 'name' => 'Physics', 'faculty_id' => $faculties['SCI']->id, 'description' => 'Focuses on physical sciences.', 'duration' => 4],
            ['code' => 'MATH', 'name' => 'Mathematics', 'faculty_id' => $faculties['SCI']->id, 'description' => 'Studies in mathematical sciences.', 'duration' => 4],
            ['code' => 'ENG', 'name' => 'English', 'faculty_id' => $faculties['ART']->id, 'description' => 'Focuses on English literature and language.', 'duration' => 4],
            ['code' => 'HIST', 'name' => 'History', 'faculty_id' => $faculties['ART']->id, 'description' => 'Studies in historical events and contexts.', 'duration' => 4],
            ['code' => 'ECON', 'name' => 'Economics', 'faculty_id' => $faculties['SOC']->id, 'description' => 'Focuses on economic theories and practices.', 'duration' => 4],
            ['code' => 'SOC', 'name' => 'Sociology', 'faculty_id' => $faculties['SOC']->id, 'description' => 'Studies in social behavior and structures.', 'duration' => 4],
            ['code' => 'PSY', 'name' => 'Psychology', 'faculty_id' => $faculties['SOC']->id, 'description' => 'Focuses on the mind and behavior.', 'duration' => 4],
            ['code' => 'LAW', 'name' => 'Law', 'faculty_id' => $faculties['LAW']->id, 'description' => 'Studies in legal systems and practices.', 'duration' => 5],
            ['code' => 'MED', 'name' => 'Medicine', 'faculty_id' => $faculties['MED']->id, 'description' => 'Focuses on medical sciences and practices.', 'duration' => 6],
            ['code' => 'NUR', 'name' => 'Nursing', 'faculty_id' => $faculties['MED']->id, 'description' => 'Studies in nursing practices.', 'duration' => 4],
            ['code' => 'PHAR', 'name' => 'Pharmacy', 'faculty_id' => $faculties['MED']->id, 'description' => 'Focuses on pharmaceutical sciences.', 'duration' => 5],
            ['code' => 'ARCH', 'name' => 'Architecture', 'faculty_id' => $faculties['ENV']->id, 'description' => 'Studies in architectural design and construction.', 'duration' => 5],
            ['code' => 'POL', 'name' => 'Political Science', 'faculty_id' => $faculties['SOC']->id, 'description' => 'Studies in political systems and behavior.', 'duration' => 4],
            ['code' => 'FIN', 'name' => 'Finance', 'faculty_id' => $faculties['BUS']->id, 'description' => 'Focuses on financial management and economics.', 'duration' => 4],
        ];


        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
