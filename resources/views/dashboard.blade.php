@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-4">
    <div class="container">
        <h1 class="mb-4">Dashboard</h1>
        
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Students</h5>
                        <p class="card-text display-4">{{ $totalStudents ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Total Staff</h5>
                        <p class="card-text display-4">{{ $totalStaff ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Departments</h5>
                        <p class="card-text display-4">{{ $totalDepartments ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Quick Actions
                    </div>
                    <div class="card-body">
                        <a href="{{ route('students.index') }}" class="btn btn-primary m-1">
                            <i class="fas fa-user-graduate"></i> Manage Students
                        </a>
                        <a href="{{ route('staff.index') }}" class="btn btn-success m-1">
                            <i class="fas fa-chalkboard-user"></i> Manage Staff
                        </a>
                        <a href="{{ route('import.form') }}" class="btn btn-info m-1">
                            <i class="fas fa-upload"></i> Import Students
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection