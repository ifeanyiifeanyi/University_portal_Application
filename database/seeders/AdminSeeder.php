<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin user
        $user = User::create([
            'user_type' => 1,
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'username' => 'super.admin',
            'slug' => Str::slug('super.admin'),
            'phone' => '1234567890',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // Create Admin record
        $admin = Admin::create([
            'user_id' => $user->id,
            'role' => 'superAdmin',
        ]);

        // Assign superAdmin role to user
        $user->assignRole('superAdmin');
    }
}
