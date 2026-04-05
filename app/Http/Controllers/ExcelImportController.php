<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessStudentImport;
use Illuminate\Support\Facades\Storage;
use App\Exports\StudentTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelImportController extends Controller
{
    public function showUploadForm()
    {
        return view('excel.upload');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120'
        ]);

        $file = $request->file('excel_file');
        $filePath = $file->store('imports');
        $originalName = $file->getClientOriginalName();

        $job = new ProcessStudentImport($filePath, $originalName);
        dispatch_sync($job);

        if ($job->hasErrors()) {
            return back()->with('error', $job->getErrorMessage())
                         ->with('import_errors', $job->getFailures());
        }

        return back()->with('success', "Import completed! {$job->getSuccessCount()} students imported successfully. Failed: {$job->getFailureCount()}");
    }

    public function downloadTemplate()
    {
        try {
            return Excel::download(new StudentTemplateExport(), 'student_import_template.xlsx');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate template: ' . $e->getMessage());
        }
    }
}
