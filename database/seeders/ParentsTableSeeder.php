<?php

namespace Database\Seeders;

use App\Models\Parents;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Parents::create([
            'user_id' => 48,
            'gender' => 'Male',
            'occupation' => 'Engineer',
            'state_of_origin' => 'Lagos',
            'lga_of_origin' => 'Ikeja',
            'hometown' => 'Ikeja',
            'residential_address' => '123 Main Street, Lagos',
            'permanent_address' => '123 Main Street, Lagos',
            'nationality' => 'Nigerian',
            'marital_status' => 'Married',
            'religion' => 'Christianity',
        ]);

        Parents::create([
            'user_id' => 49,
            'gender' => 'Female',
            'occupation' => 'Teacher',
            'state_of_origin' => 'Oyo',
            'lga_of_origin' => 'Ibadan North',
            'hometown' => 'Ibadan',
            'residential_address' => '456 Another Street, Ibadan',
            'permanent_address' => '456 Another Street, Ibadan',
            'nationality' => 'Nigerian',
            'marital_status' => 'Single',
            'religion' => 'Islam',
        ]);
    }
}
