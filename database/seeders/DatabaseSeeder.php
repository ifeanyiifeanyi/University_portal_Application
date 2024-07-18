<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
<<<<<<< HEAD
=======
use App\Models\Admin;
>>>>>>> origin/master
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
<<<<<<< HEAD
        User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
=======


        // $this->call(UserTeacherStudentSeeder::class);
        $this->call([
            AcademicSessionSeeder::class,
            CourseSeeder::class,
            FacultySeeder::class,
            DepartmentSeeder::class,
            SemesterSeeder::class,
            UserTeacherStudentSeeder::class
        ]);



>>>>>>> origin/master
    }
}
