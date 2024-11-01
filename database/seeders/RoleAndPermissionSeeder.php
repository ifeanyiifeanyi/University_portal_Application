<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: Clear existing roles and permissions
        Role::query()->delete();
        Permission::query()->delete();
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // Dashboard
            'view dashboard',

            // TimeTable Manager
            'view timetable',
            'create timetable',
            'edit timetable',
            'delete timetable',
            'view draft timetable',

            // Payment Manager
            'manage payment types',
            'manage payment methods',
            'view invoice manager',
            'pay fees',

            //attendance manager
            'view attendance',
            'create attendance',
            'edit attendance',
            'delete attendance',


            // Payment Types
            'view payment types',
            'create payment types',
            'edit payment types',
            'delete payment types',
            'process payments',

            // App Manager
            'manage academic sessions',
            'manage semester',
            'manage faculties',
            'manage departments',
            'manage courses',
            'assign department course credits',

            // Academics Manager
            'manage students',
            'create students',
            'edit students',
            'update students',
            'delete students',
            'view student records',
            'view student scores',
            'view student course registrations',

            // Lecturers Management
            'manage lecturers',
            'create lecturers', // Remove the duplicate
            'edit lecturers',
            'delete lecturers',
            'update lecturers',

            // Academic Records
            'assign semester courses to department',
            'assign department courses to lecturers',
            'approve student scores',
            'audit student scores',
            'manage student course registrations',
            'manage student scores',

            'manage course levels',
            'view roles',
            'edit roles',
            'assign roles',



            // Notifications
            'manage notifications',
            'view notifications',

            // Administrators
            'view administrators',
            'create administrator',
            'edit administrator',
            'update administrator',
            'delete administrator',

            'manage attendance',
            'view grades',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign existing permissions
        $superAdmin = Role::firstOrCreate(['name' => 'superAdmin']);
        $superAdmin->givePermissionTo(Permission::all()); // All permissions for superAdmin

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        // You can customize permissions for the admin role
        $adminRole->givePermissionTo([
            'view timetable',
            'manage lecturers',
            'manage students',
            'manage academic sessions',
            'process payments',
            'manage departments',
        ]);

        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        // Minimal permissions for staff
        $staffRole->givePermissionTo([
            'view timetable',
            'view notifications'
        ]);
    }
}
