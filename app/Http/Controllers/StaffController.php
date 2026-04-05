<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Department;
use Yajra\DataTables\Facades\DataTables;

class StaffController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        return view('staff.index', compact('departments'));
    }

    public function getData(Request $request)
    {
        $staff = Staff::with('department')->select('staff.*');
        
        return DataTables::of($staff)
            ->addColumn('department_name', function($staff) {
                return $staff->department->name;
            })
            ->filterColumn('department_name', function($query, $keyword) {
                $query->whereHas('department', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:staff,email',
            'employee_id' => 'required|string|max:50|unique:staff,employee_id',
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $staff = Staff::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Staff member created successfully!',
            'staff' => $staff->load('department')
        ]);
    }
}
