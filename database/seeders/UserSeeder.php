<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user first (Human Capital Lead)
        User::create([
            'name' => 'Rahadiyan Purba',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('daffa123'),
            'role' => 'admin',
            'department_id' => $this->getDepartmentId('Human Resources'),
            'employee_id' => 'EMP-001',
            'position' => 'Lead of Human Capital',
            'phone' => '1234567890'
        ]);

        // Create IT support user
        User::create([
            'name' => 'Daffa Fakhuddin Arrozy',
            'email' => 'daffatgi02@gmail.com',
            'password' => Hash::make('daffa123'),
            'role' => 'it_support',
            'department_id' => $this->getDepartmentId('Information Technology'),
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
            'department_id' => $this->getDepartmentId('General Affairs'),
            'employee_id' => 'EMP-003',
            'position' => 'General Affairs Officer',
            'phone' => '1234567892'
        ]);

        // Create regular users from the provided list
        $this->createRegularUser('Jose Amadeus Abdi A.L.P', 'Director', 'Management');

        // Finance Department
        $this->createRegularUser('Dinda Budiarti', 'Lead of Finance', 'Finance');
        $this->createRegularUser('Rika Nidiawati', 'Staff Account Receivable', 'Finance');
        $this->createRegularUser('Syahril Qudus Ibnu Ahmad', 'SPV Finance & Tax', 'Finance');

        // Creative Department
        $this->createRegularUser('Tri Sapta Mahardika', 'SPV Creative Team', 'Marketing');
        $this->createRegularUser('Hizkia Yanuar Pambudi', 'Staff Content Creator B2C', 'Marketing');
        $this->createRegularUser('Andhika Yogatama Yanuar', 'Staff Content Creator B2C', 'Marketing');
        $this->createRegularUser('Rizka Zahara', 'Staff Content Creator B2C', 'Marketing');
        $this->createRegularUser('Refo Ganggawasa Utomo', 'Staff Content Creator B2B', 'Marketing');
        $this->createRegularUser('R. Ibnu Wicaksono Wibowo', 'Staff Design', 'Marketing');
        $this->createRegularUser('Salistya Adi Nugraha', 'Staff Design', 'Marketing');

        // Admin/Document Control
        $this->createRegularUser('Serli Indriani', 'Staff Document Control', 'General Affairs');
        $this->createRegularUser('Vita Oktaviari', 'Admin Marketplace', 'Sales');

        // Operations Department
        $this->createRegularUser('Anang Siswanto', 'Manager Operasional', 'Operations');
        $this->createRegularUser('Dimas Bhranta Putera Adi', 'Lead of Operation', 'Operations');
        $this->createRegularUser('Endru Riski Hermansya', 'Staff Operasional & Maintenance', 'Operations');
        $this->createRegularUser('Bayu Budi Prasetyo', 'Supervisor Operation & Maintenance', 'Operations');
        $this->createRegularUser('Rofiul Fajri Kurniawan', 'Staff ONM', 'Operations');

        // Supply Chain & Warehouse
        $this->createRegularUser('Wicaksono Aji Pamungkas', 'SPV Packaging Designer', 'Operations');
        $this->createRegularUser('Satria Ganda', 'Staff Warehouse', 'Operations');
        $this->createRegularUser('Ahmad Nurrosad', 'Staff PPIC', 'Operations');
        $this->createRegularUser('Ricky Aditya Permana', 'Staff Purchasing', 'Operations');
        $this->createRegularUser('Aris Sudarisman', 'Staff Supply Planner', 'Operations');
        $this->createRegularUser('Ratri Yuliana', 'Admin Purchasing', 'Operations');

        // Quality Control & R&D
        $this->createRegularUser('Riana Kusniawati', 'Supervisor Quality Assurance', 'Quality Control');
        $this->createRegularUser('Doni Cipta Renada', 'Staff RND', 'Research and Development');
        $this->createRegularUser('Aisyah Qurota Ayun', 'Staff QC', 'Quality Control');
        $this->createRegularUser('Zaiful Richi Nurrohmat', 'Staff QC', 'Quality Control');
        $this->createRegularUser('Muhammad Fuad Al Khafiz', 'Formulator', 'Research and Development');
        $this->createRegularUser('Ahmad Najib', 'Formulator', 'Research and Development');

        // Regulatory
        $this->createRegularUser('Kania Gayatri', 'Staff Regulatory', 'Regulatory');
        $this->createRegularUser('Rodhiyah Binti Sholehah', 'Staff Regulatory', 'Regulatory');

        // Human Capital
        $this->createRegularUser('Rinda Meka Brawati', 'Supervisor Human Capital', 'Human Resources');

        // Customer Service
        $this->createRegularUser('Lilin Indah Khansa Khairun', 'CS', 'Customer Service');

        // Sales & Marketing
        $this->createRegularUser('Mochammad Yunus', 'Sales, Marketing & Business Development Manager', 'Sales');
        $this->createRegularUser('Wahyu Ghita Setiawan', 'Supervisor of Sales', 'Sales');
        $this->createRegularUser('Aryo Wicaksono', 'Market Research and Media Development', 'Marketing');
    }

    private function createRegularUser($name, $position, $department)
    {
        // Generate email from name (lowercase, no spaces, add @company.com)
        $email = strtolower(str_replace(' ', '.', $name)) . '@wijayainovasi.co.id';

        // Generate employee ID
        static $counter = 4; // Start after the initial 3 users
        $employeeId = 'EMP-' . str_pad($counter++, 3, '0', STR_PAD_LEFT);

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('123456'), // Default password
            'role' => 'user',
            'department_id' => $this->getDepartmentId($department),
            'employee_id' => $employeeId,
            'position' => $position,
            'phone' => '00000' . rand(10000000, 99999999) // Random Indonesian phone number
        ]);
    }

    private function getDepartmentId($departmentName)
    {
        $department = Department::where('name', $departmentName)->first();

        // If department doesn't exist yet, create it
        if (!$department) {
            $department = Department::create([
                'name' => $departmentName,
                'description' => $departmentName . ' Department'
            ]);
        }

        return $department->id;
    }
}
