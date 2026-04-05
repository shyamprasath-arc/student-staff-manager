<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Department;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalStaff = Staff::count();
        $totalDepartments = Department::count();
        
        return view('dashboard', compact('totalStudents', 'totalStaff', 'totalDepartments'));
    }
}
