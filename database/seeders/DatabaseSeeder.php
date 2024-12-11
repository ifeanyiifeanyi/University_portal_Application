<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Admin;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        // $this->call(UserTeacherStudentSeeder::class);
        $this->call([
            // AcademicSessionSeeder::class,
            // CourseSeeder::class,
            // FacultySeeder::class,
            // DepartmentSeeder::class,
            // SemesterSeeder::class,
            // UserTeacherStudentSeeder::class,
            RoleAndPermissionSeeder::class,
            // AdminSeeder::class,
        ]);



    }
}
