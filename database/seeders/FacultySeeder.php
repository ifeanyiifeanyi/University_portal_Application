<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faculties = [
            ['code' => 'SCI', 'name' => 'Faculty of Science'],
            ['code' => 'ENG', 'name' => 'Faculty of Engineering'],
            ['code' => 'ART', 'name' => 'Faculty of Arts'],
            ['code' => 'SOC', 'name' => 'Faculty of Social Sciences'],
            ['code' => 'LAW', 'name' => 'Faculty of Law'],
            ['code' => 'MED', 'name' => 'Faculty of Medicine'],
            ['code' => 'ENV', 'name' => 'Faculty of Environmental Studies'],
            ['code' => 'BUS', 'name' => 'Faculty of Business Administration'],
            ['code' => 'EDU', 'name' => 'Faculty of Education'],
            ['code' => 'AGR', 'name' => 'Faculty of Agriculture'],
            ['code' => 'PHM', 'name' => 'Faculty of Pharmacy'],
            ['code' => 'VET', 'name' => 'Faculty of Veterinary Medicine'],
            ['code' => 'HUM', 'name' => 'Faculty of Humanities'],
            ['code' => 'COM', 'name' => 'Faculty of Communication'],
            ['code' => 'FMS', 'name' => 'Faculty of Management Sciences'],
            ['code' => 'DENT', 'name' => 'Faculty of Dentistry'],
            ['code' => 'MLS', 'name' => 'Faculty of Medical Laboratory Science'],
            ['code' => 'PUBH', 'name' => 'Faculty of Public Health'],
            ['code' => 'SPA', 'name' => 'Faculty of Sports and Physical Activity'],
            ['code' => 'COMP', 'name' => 'Faculty of Computer Science'],
        ];



        foreach ($faculties as $faculty) {
            Faculty::create($faculty);
        }
    }
}
