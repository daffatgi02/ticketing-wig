<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Hardware Issue',
                'description' => 'Problems with physical computer hardware, peripherals, or equipment'
            ],
            [
                'name' => 'Software Issue',
                'description' => 'Problems with installed software, applications, or operating systems'
            ],
            [
                'name' => 'Network Issue',
                'description' => 'Internet connectivity, Wi-Fi, VPN, or network access problems'
            ],
            [
                'name' => 'Account Management',
                'description' => 'Requests related to user accounts, passwords, or access permissions'
            ],
            [
                'name' => 'Office Equipment',
                'description' => 'Issues with printers, scanners, phones, or other office equipment'
            ],
            [
                'name' => 'Facilities Request',
                'description' => 'Requests related to office facilities, furniture, or environment'
            ],
            [
                'name' => 'General Inquiry',
                'description' => 'General questions, information requests, or other inquiries'
            ],
            [
                'name' => 'Other',
                'description' => 'Other issues not covered by the existing categories'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
