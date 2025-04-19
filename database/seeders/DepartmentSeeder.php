<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            [
                'name' => 'Human Resources',
                'description' => 'Manages employee relations, recruitment, and workplace policies'
            ],
            [
                'name' => 'Information Technology',
                'description' => 'Manages the company\'s technology infrastructure and IT support'
            ],
            [
                'name' => 'Finance',
                'description' => 'Handles financial operations, budgeting, and accounting'
            ],
            [
                'name' => 'Operations',
                'description' => 'Oversees daily business operations and logistics'
            ],
            [
                'name' => 'Marketing',
                'description' => 'Develops and executes marketing strategies'
            ],
            [
                'name' => 'Sales',
                'description' => 'Manages sales activities and customer relationships'
            ],
            [
                'name' => 'General Affairs',
                'description' => 'Manages office facilities, equipment, and general support'
            ]
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
