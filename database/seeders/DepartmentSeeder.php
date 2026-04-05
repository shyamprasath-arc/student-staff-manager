<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Computer Science and Engineering', 'code' => 'CSE', 'description' => 'Computer Science programs'],
            ['name' => 'Information Technology', 'code' => 'IT', 'description' => 'Information Technology programs'],
            ['name' => 'Electronics and Communication', 'code' => 'ECE', 'description' => 'Electronics programs'],
            ['name' => 'Mechanical Engineering', 'code' => 'MECH', 'description' => 'Mechanical programs'],
            ['name' => 'Business Administration', 'code' => 'MBA', 'description' => 'Management programs'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
