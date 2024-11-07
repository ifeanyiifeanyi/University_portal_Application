<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Department;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserTeacherStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $randomImages = [
            'https://img.freepik.com/free-photo/confident-african-businesswoman-smiling-closeup-portrait-jobs-career-campaign_53876-143280.jpg',
            'https://img.freepik.com/free-photo/confident-entrepreneur-looking-camera-with-arms-folded-smiling_1098-18840.jpg',
            'https://img.freepik.com/free-photo/confident-african-businesswoman-smiling-closeup-portrait-jobs-career-campaign_53876-143280.jpg',
            'https://img.freepik.com/free-photo/confident-entrepreneur-looking-camera-with-arms-folded-smiling_1098-18840.jpg',
            'https://img.freepik.com/free-photo/confident-african-businesswoman-smiling-closeup-portrait-jobs-career-campaign_53876-143280.jpg',
            'https://img.freepik.com/free-photo/confident-entrepreneur-looking-camera-with-arms-folded-smiling_1098-18840.jpg',
            'https://img.freepik.com/free-photo/confident-african-businesswoman-smiling-closeup-portrait-jobs-career-campaign_53876-143280.jpg',
            'https://img.freepik.com/free-photo/confident-entrepreneur-looking-camera-with-arms-folded-smiling_1098-18840.jpg',
            'https://img.freepik.com/free-photo/confident-african-businesswoman-smiling-closeup-portrait-jobs-career-campaign_53876-143280.jpg',
            'https://img.freepik.com/free-photo/confident-entrepreneur-looking-camera-with-arms-folded-smiling_1098-18840.jpg',
        ];

        $faker = Faker::create();

        // Create 5 admins
        for ($i = 0; $i < 5; $i++) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $username = Str::lower($firstName . '.' . $lastName);

            $user = User::create([
                'user_type' => 1,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'other_name' => $faker->optional()->firstName,
                'username' => $username,
                'slug' => Str::slug($username),
                'phone' => $faker->phoneNumber,
                'email' => $faker->unique()->safeEmail,
                'profile_photo' => $randomImages[rand(0, 10)],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]);
            Admin::create([
                'user_id' => $user->id,
                'role' => $faker->randomElement(['superAdmin', 'admin', 'staff']),
            ]);
        }

        // // Create 10 teachers
        // // Create 10 teachers
        // for ($i = 0; $i < 20; $i++) {
        //     $firstName = $faker->firstName;
        //     $lastName = $faker->lastName;
        //     $username = Str::lower($firstName . '.' . $lastName);

        //     $user = User::create([
        //         'user_type' => 2,
        //         'first_name' => $firstName,
        //         'last_name' => $lastName,
        //         'other_name' => $faker->optional()->firstName,
        //         'username' => $username,
        //         'slug' => Str::slug($username),
        //         'phone' => $faker->phoneNumber,
        //         'email' => $faker->unique()->safeEmail,
        //         'profile_photo' => $randomImages[rand(0, 10)],
        //         'email_verified_at' => now(),
        //         'password' => Hash::make('password'),
        //     ]);

        //     Teacher::create([
        //         'user_id' => $user->id,
        //         'date_of_birth' => $faker->date,
        //         'gender' => $faker->randomElement(['Male', 'Female']),
        //         'teaching_experience' => $faker->numberBetween(1, 20) . ' years',
        //         'teacher_type' => $faker->randomElement(['Full-time', 'Part-time', 'Auxiliary']),
        //         'teacher_qualification' => $faker->randomElement(['Bachelor of Science', 'Master of Computer Science', 'PhD in Software Engineering']),
        //         'teacher_title' => $faker->randomElement(['Mr.', 'Mrs.', 'Dr.', 'Prof.']),
        //         'office_hours' => $faker->randomElement(['Mon-Wed 9AM-12PM', 'Tue-Thu 1PM-4PM', 'Wed-Fri 10AM-2PM']),
        //         'office_address' => $faker->secondaryAddress,
        //         'biography' => $faker->paragraph,
        //         'certifications' => json_encode($faker->randomElements(['CCNA', 'AWS Certified', 'Microsoft Certified Educator', 'Google Certified Teacher'], $faker->numberBetween(1, 3))),
        //         'publications' => json_encode([
        //             $faker->sentence . ' - ' . $faker->date('Y'),
        //             $faker->sentence . ' - ' . $faker->date('Y')
        //         ]),
        //         'number_of_awards' => $faker->numberBetween(0, 10),
        //         'employment_id' => $faker->unique()->numberBetween(111111111, 99999999),
        //         'date_of_employment' => $faker->date,
        //         'address' => $faker->address,
        //         'nationality' => $faker->country,
        //         'level' => $faker->randomElement(['Senior Lecturer', 'Junior Lecturer', 'Technician']),
        //     ]);
        // }

        // // ! student seeder
        // for ($i = 0; $i < 50; $i++) {
        //     $firstName = $faker->firstName;
        //     $lastName = $faker->lastName;
        //     $username = Str::lower($firstName . '.' . $lastName);

        //     $user = User::create([
        //         'user_type' => 3,
        //         'first_name' => $firstName,
        //         'last_name' => $lastName,
        //         'other_name' => $faker->optional()->firstName,
        //         'username' => $username,
        //         'slug' => Str::slug($username),
        //         'phone' => $faker->phoneNumber,
        //         'email' => $faker->unique()->safeEmail,
        //         'profile_photo' => null,
        //         'email_verified_at' => now(),
        //         'password' => Hash::make('password'),
        //     ]);

        //     $departments = Department::pluck('id')->toArray();

        //     Student::create([
        //         'user_id' => $user->id,
        //         'department_id' => $faker->randomElement($departments),
        //         'matric_number' => $faker->unique()->numerify('UNI######'),
        //         'date_of_birth' => $faker->date('Y-m-d', '-18 years'),
        //         'gender' => $faker->randomElement(['Male', 'Female', 'Other']),
        //         'state_of_origin' => $faker->state,
        //         'lga_of_origin' => $faker->city,
        //         'hometown' => $faker->city,
        //         'residential_address' => $faker->address,
        //         'permanent_address' => $faker->address,
        //         'nationality' => 'Nigerian',
        //         'marital_status' => $faker->randomElement(['Single', 'Married', 'Divorced', 'Widowed']),
        //         'religion' => $faker->randomElement(['Christianity', 'Islam', 'Traditional', 'Other']),
        //         'blood_group' => $faker->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
        //         'genotype' => $faker->randomElement(['AA', 'AS', 'SS', 'AC']),
        //         'next_of_kin_name' => $faker->name,
        //         'next_of_kin_relationship' => $faker->randomElement(['Parent', 'Sibling', 'Spouse', 'Uncle', 'Aunt']),
        //         'next_of_kin_phone' => $faker->phoneNumber,
        //         'next_of_kin_address' => $faker->address,
        //         'jamb_registration_number' => $faker->optional()->numerify('########EF'),
        //         'year_of_admission' => $faker->year,
        //         'mode_of_entry' => $faker->randomElement(['UTME', 'Direct Entry', 'Transfer']),
        //         'current_level' => $faker->randomElement(['100', '200', '300', '400', '500']),
        //         'cgpa' => $faker->optional()->randomFloat(2, 1, 5),
        //     ]);
        // }
    }
}
