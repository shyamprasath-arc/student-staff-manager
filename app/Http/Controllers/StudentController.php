<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Programme;
use App\Models\Department;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class StudentController extends Controller
{
    public function index()
    {
        $departments = Department::with('programmes')->get();
        return view('students.index', compact('departments'));
    }

    public function getData(Request $request)
    {
        $students = Student::with(['department', 'programme'])->select('students.*');
        
        return DataTables::of($students)
            ->addColumn('department_name', function($student) {
                return $student->department->name;
            })
            ->addColumn('programme_name', function($student) {
                return $student->programme->name;
            })
            ->filterColumn('department_name', function($query, $keyword) {
                $query->whereHas('department', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('programme_name', function($query, $keyword) {
                $query->whereHas('programme', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getProgrammes($departmentId)
    {
        $programmes = Programme::where('department_id', $departmentId)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);
        
        return response()->json($programmes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:students,email',
            'registration_number' => 'required|string|max:50|unique:students,registration_number',
            'department_id' => 'required|exists:departments,id',
            'programme_id' => [
                'required',
                'exists:programmes,id',
                Rule::exists('programmes', 'id')->where(function ($query) use ($request) {
                    $query->where('department_id', $request->department_id);
                }),
            ],
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
        ]);

        $student = Student::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student created successfully!',
            'student' => $student->load(['department', 'programme'])
        ]);
    }
}
