<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AcademicSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sessions = [
            [
                'name' => '2023/2024',
                'start_date' => '2023-09-01',
                'end_date' => '2024-08-31',
                'is_current' => true,
            ],
            [
                'name' => '2024/2025',
                'start_date' => '2024-09-01',
                'end_date' => '2025-08-31',
                'is_current' => false,
            ],
        ];

        foreach ($sessions as $session) {
            AcademicSession::create($session);
        }

    }
}
