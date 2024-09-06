<?php

namespace Database\Seeders;

use App\Models\Attendancee;
use App\Models\Createattendancee;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateattendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Attendancee::create([
            'teacher_id'=>1,
            'course_id'=>17,
            'student_id'=>1,
            'academic_session_id'=>1,
            'semester_id'=>1,
            'department_id'=>1,
            'lecture_date'=>Carbon::now(),
            'status'=>'present',
            'remarks'=>'all students are active',
        ]);
    }
}
