<?php

namespace Database\Seeders;

use App\Models\TeacherAssignment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherAssigmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherassignments = [
            [
                'teacher_id' => 1,
                'department_id' => 1,
                'academic_session_id' => 1,
                'semester_id'=>1,
                'course_id'=>17
            ],
            [
                'teacher_id' => 1,
                'department_id' => 1,
                'academic_session_id' => 1,
                'semester_id'=>1,
                'course_id'=>4
            ]
           
            
        ];
        foreach ($teacherassignments as $teacherassignment) {
            TeacherAssignment::create($teacherassignment);
        }
    }
}
