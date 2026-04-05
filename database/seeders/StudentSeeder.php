<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Department;
use App\Models\Programme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'registration_number' => 'REG2024001',
                'department' => 'Computer Science and Engineering',
                'programme' => 'B.Tech Computer Science',
                'phone' => '+1234567890',
                'dob' => '2002-05-15'
            ],
            [
                'name' => 'Emily Johnson',
                'email' => 'emily.johnson@example.com',
                'registration_number' => 'REG2024002',
                'department' => 'Computer Science and Engineering',
                'programme' => 'BCA',
                'phone' => '+1234567891',
                'dob' => '2003-03-22'
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@example.com',
                'registration_number' => 'REG2024003',
                'department' => 'Information Technology',
                'programme' => 'B.Tech Information Technology',
                'phone' => '+1234567892',
                'dob' => '2002-08-10'
            ],
            [
                'name' => 'Sarah Davis',
                'email' => 'sarah.davis@example.com',
                'registration_number' => 'REG2024004',
                'department' => 'Electronics and Communication',
                'programme' => 'B.E Electronics',
                'phone' => '+1234567893',
                'dob' => '2003-01-18'
            ],
            [
                'name' => 'Robert Wilson',
                'email' => 'robert.wilson@example.com',
                'registration_number' => 'REG2024005',
                'department' => 'Mechanical Engineering',
                'programme' => 'B.E Mechanical',
                'phone' => '+1234567894',
                'dob' => '2002-11-25'
            ],
            [
                'name' => 'Jessica Martinez',
                'email' => 'jessica.martinez@example.com',
                'registration_number' => 'REG2024006',
                'department' => 'Business Administration',
                'programme' => 'BBA',
                'phone' => '+1234567895',
                'dob' => '2003-07-08'
            ],
            [
                'name' => 'David Anderson',
                'email' => 'david.anderson@example.com',
                'registration_number' => 'REG2024007',
                'department' => 'Computer Science and Engineering',
                'programme' => 'M.Tech Computer Science',
                'phone' => '+1234567896',
                'dob' => '2001-09-14'
            ],
            [
                'name' => 'Lisa Thompson',
                'email' => 'lisa.thompson@example.com',
                'registration_number' => 'REG2024008',
                'department' => 'Information Technology',
                'programme' => 'MCA',
                'phone' => '+1234567897',
                'dob' => '2002-12-03'
            ],
            [
                'name' => 'James Garcia',
                'email' => 'james.garcia@example.com',
                'registration_number' => 'REG2024009',
                'department' => 'Electronics and Communication',
                'programme' => 'M.E Electronics',
                'phone' => '+1234567898',
                'dob' => '2001-06-30'
            ],
            [
                'name' => 'Maria Rodriguez',
                'email' => 'maria.rodriguez@example.com',
                'registration_number' => 'REG2024010',
                'department' => 'Business Administration',
                'programme' => 'MBA',
                'phone' => '+1234567899',
                'dob' => '2001-04-17'
            ]
        ];

        foreach ($students as $studentData) {
            $department = Department::where('name', $studentData['department'])->first();
            $programme = Programme::where('name', $studentData['programme'])
                ->where('department_id', $department->id)
                ->first();

            if ($department && $programme) {
                Student::create([
                    'name' => $studentData['name'],
                    'email' => $studentData['email'],
                    'registration_number' => $studentData['registration_number'],
                    'department_id' => $department->id,
                    'programme_id' => $programme->id,
                    'phone' => $studentData['phone'],
                    'dob' => $studentData['dob'],
                ]);
            }
        }
    }
}
