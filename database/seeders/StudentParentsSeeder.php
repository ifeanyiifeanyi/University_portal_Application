<?php

namespace Database\Seeders;

use App\Models\Parents;
use App\Models\StudentsParent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentParentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StudentsParent::create([
            'parent_id' => 1,
            'student_id' => 4,
            'status' => 'approved',
            'parent_type' => 'Father',
        ]);

        StudentsParent::create([
            'parent_id' => 2,
            'student_id' => 2,
            'status' => 'pending',
            'parent_type' => 'Mother',
        ]);
    }
}
