<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Programme;
use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StudentsImport implements ToCollection, WithHeadingRow
{
    private $successCount = 0;
    private $failures = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                DB::beginTransaction();
                
                // Skip empty rows
                if (empty($row['name']) && empty($row['email']) && empty($row['registration_number'])) {
                    DB::rollBack();
                    continue;
                }
                
                // Validate required fields
                $errors = [];
                
                if (empty($row['name'])) {
                    $errors[] = 'Name is required';
                }
                
                if (empty($row['email'])) {
                    $errors[] = 'Email is required';
                } elseif (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Invalid email format';
                }
                
                if (empty($row['registration_number'])) {
                    $errors[] = 'Registration number is required';
                }
                
                if (empty($row['department'])) {
                    $errors[] = 'Department is required';
                }
                
                if (empty($row['programme'])) {
                    $errors[] = 'Programme is required';
                }
                
                if (!empty($errors)) {
                    $this->failures[] = [
                        'row' => $index + 2,
                        'registration_number' => $row['registration_number'] ?? 'N/A',
                        'errors' => $errors
                    ];
                    DB::rollBack();
                    continue;
                }

                // Find department
                $department = Department::where('name', 'like', trim($row['department']))->first();
                if (!$department) {
                    $this->failures[] = [
                        'row' => $index + 2,
                        'registration_number' => $row['registration_number'],
                        'errors' => ['Department "' . $row['department'] . '" not found in system']
                    ];
                    DB::rollBack();
                    continue;
                }

                // Find programme
                $programme = Programme::where('name', 'like', trim($row['programme']))
                    ->where('department_id', $department->id)
                    ->first();
                
                if (!$programme) {
                    $this->failures[] = [
                        'row' => $index + 2,
                        'registration_number' => $row['registration_number'],
                        'errors' => ['Programme "' . $row['programme'] . '" not found under department "' . $row['department'] . '"']
                    ];
                    DB::rollBack();
                    continue;
                }

                // Check for existing student
                $existingStudent = Student::where('email', $row['email'])
                    ->orWhere('registration_number', $row['registration_number'])
                    ->first();
                
                if ($existingStudent) {
                    $this->failures[] = [
                        'row' => $index + 2,
                        'registration_number' => $row['registration_number'],
                        'errors' => ['Student with email ' . $row['email'] . ' or registration number ' . $row['registration_number'] . ' already exists']
                    ];
                    DB::rollBack();
                    continue;
                }

                // Create student
                Student::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'registration_number' => $row['registration_number'],
                    'department_id' => $department->id,
                    'programme_id' => $programme->id,
                    'phone' => $row['phone'] ?? null,
                    'dob' => !empty($row['dob']) ? date('Y-m-d', strtotime($row['dob'])) : null,
                ]);

                DB::commit();
                $this->successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->failures[] = [
                    'row' => $index + 2,
                    'registration_number' => $row['registration_number'] ?? 'N/A',
                    'errors' => ['Database error: ' . $e->getMessage()]
                ];
                Log::error("Student import error at row " . ($index + 2) . ": " . $e->getMessage());
            }
        }
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getFailures()
    {
        return $this->failures;
    }
}