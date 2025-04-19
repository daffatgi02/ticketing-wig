<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Rahadiyan Purba',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('daffa123'),
            'role' => 'admin',
            'department_id' => 1, // Human Resources
            'employee_id' => 'EMP-001',
            'position' => 'System Administrator',
            'phone' => '1234567890'
        ]);

        // Create IT support user
        User::create([
            'name' => 'Daffa Fakhuddin Arrozy',
            'email' => 'daffatgi02@gmail.com',
            'password' => Hash::make('daffa123'),
            'role' => 'it_support',
            'department_id' => 2, // IT
            'employee_id' => 'EMP-002',
            'position' => 'IT Support',
            'phone' => '1234567891'
        ]);

        // Create GA support user
        User::create([
            'name' => 'Agus Widardi',
            'email' => 'it.wijayainovasigemilang@gmail.com',
            'password' => Hash::make('daffa123'),
            'role' => 'ga_support',
            'department_id' => 7, // General Affairs
            'employee_id' => 'EMP-003',
            'position' => 'General Affairs Officer',
            'phone' => '1234567892'
        ]);

        // Create regular users
        User::create([
            'name' => 'Rika Nidiawati',
            'email' => 'finance@gmail.com',
            'password' => Hash::make('daffa123'),
            'role' => 'user',
            'department_id' => 3, // Finance
            'employee_id' => 'EMP-004',
            'position' => 'Finance',
            'phone' => '1234567893'
        ]);
    }
}
