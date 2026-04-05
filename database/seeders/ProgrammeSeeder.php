<?php

namespace Database\Seeders;

use App\Models\Programme;
use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgrammeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programmes = [
            // CSE Department
            ['name' => 'B.Tech Computer Science', 'code' => 'CS101', 'department' => 'CSE', 'duration' => 4],
            ['name' => 'M.Tech Computer Science', 'code' => 'CS201', 'department' => 'CSE', 'duration' => 2],
            ['name' => 'BCA', 'code' => 'CS301', 'department' => 'CSE', 'duration' => 3],
            
            // IT Department
            ['name' => 'B.Tech Information Technology', 'code' => 'IT101', 'department' => 'IT', 'duration' => 4],
            ['name' => 'MCA', 'code' => 'IT201', 'department' => 'IT', 'duration' => 3],
            
            // ECE Department
            ['name' => 'B.E Electronics', 'code' => 'EC101', 'department' => 'ECE', 'duration' => 4],
            ['name' => 'M.E Electronics', 'code' => 'EC201', 'department' => 'ECE', 'duration' => 2],
            
            // MECH Department
            ['name' => 'B.E Mechanical', 'code' => 'ME101', 'department' => 'MECH', 'duration' => 4],
            
            // MBA Department
            ['name' => 'BBA', 'code' => 'MB101', 'department' => 'MBA', 'duration' => 3],
            ['name' => 'MBA', 'code' => 'MB201', 'department' => 'MBA', 'duration' => 2],
        ];

        foreach ($programmes as $prog) {
            $department = Department::where('code', $prog['department'])->first();
            if ($department) {
                Programme::create([
                    'name' => $prog['name'],
                    'code' => $prog['code'],
                    'department_id' => $department->id,
                    'duration_years' => $prog['duration'],
                ]);
            }
        }
    }
}
