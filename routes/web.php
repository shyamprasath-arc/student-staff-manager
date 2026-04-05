<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExcelImportController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Student Management
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students-data', [StudentController::class, 'getData'])->name('students.data');
    Route::get('/get-programmes/{department}', [StudentController::class, 'getProgrammes'])->name('get.programmes');
    
    // Staff Management
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff-data', [StaffController::class, 'getData'])->name('staff.data');
    
    // Excel Import
    Route::get('/import-students', [ExcelImportController::class, 'showUploadForm'])->name('import.form');
    Route::post('/import-students', [ExcelImportController::class, 'import'])->name('import.process');

    // for downloading template
    Route::get('/download-template', [ExcelImportController::class, 'downloadTemplate'])->name('import.template');
});

require __DIR__.'/auth.php';