<?php

namespace App\Jobs;

use App\Imports\StudentsImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ProcessStudentImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $filePath;
    protected $originalName;
    protected $failures = [];
    protected $successCount = 0;
    protected $failureCount = 0;
    protected $errorMessage = null;

    public function __construct($filePath, $originalName = null)
    {
        $this->filePath = $filePath;
        $this->originalName = $originalName;
    }

    public function handle()
    {
        try {
            Log::info("Starting Excel import", ['file' => $this->originalName]);
            
            $import = new StudentsImport();
            Excel::import($import, $this->filePath);
            
            $this->failures = $import->getFailures();
            $this->failureCount = count($this->failures);
            $this->successCount = $import->getSuccessCount();
            
            Log::info("Excel Import completed", [
                'file' => $this->originalName,
                'success' => $this->successCount,
                'failed' => $this->failureCount,
                'failures' => $this->failures
            ]);
            
            // Clean up the uploaded file
            Storage::delete($this->filePath);
            
        } catch (Throwable $e) {
            $this->errorMessage = "Import failed: " . $e->getMessage();
            Log::error($this->errorMessage, ['trace' => $e->getTraceAsString()]);
            
            // Clean up on error as well
            Storage::delete($this->filePath);
        }
    }

    public function hasErrors()
    {
        return !is_null($this->errorMessage) || $this->failureCount > 0;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage ?? "Import completed with {$this->failureCount} errors.";
    }

    public function getFailures()
    {
        return $this->failures;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getFailureCount()
    {
        return $this->failureCount;
    }
}