@extends('layouts.app')

@section('title', 'Import Students via Excel')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-upload"></i> Bulk Import Students</h3>
                </div>
                <div class="card-body">
                    @if(session('import_errors'))
                        <div class="alert alert-warning">
                            <strong><i class="fas fa-exclamation-triangle"></i> Partial Import Completed:</strong> Some rows failed validation.
                            <button type="button" class="btn btn-link btn-sm" data-bs-toggle="collapse" data-bs-target="#errorDetails">
                                View Details
                            </button>
                            <div id="errorDetails" class="collapse mt-2">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Row #</th>
                                                <th>Registration Number</th>
                                                <th>Errors</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(session('import_errors') as $failure)
                                                <tr class="table-danger">
                                                    <td>{{ $failure['row'] }}</td>
                                                    <td>{{ $failure['registration_number'] }}</td>
                                                    <td>
                                                        <ul class="mb-0">
                                                            @foreach($failure['errors'] as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Quick Guide:</h5>
                                <ol class="mb-0">
                                    <li>Download the Excel template below</li>
                                    <li>Fill in student data (required fields marked with *)</li>
                                    <li>Upload the completed file using the form</li>
                                    <li>Review import results</li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <h5><i class="fas fa-download"></i> Download Template</h5>
                                <p class="mb-2">Get the Excel template with proper headers and examples:</p>
                                <a href="{{ route('import.template') }}" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-file-excel"></i> Download Excel Template
                                </a>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <form action="{{ route('import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">
                                <i class="fas fa-file-upload"></i> Upload Excel File <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control @error('excel_file') is-invalid @enderror" 
                                   id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                            @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Supported formats: .xlsx, .xls, .csv (Max size: 5MB)
                            </small>
                        </div>
                        
                        <div class="alert alert-secondary">
                            <h6><i class="fas fa-table"></i> Column Format:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Column Name</th>
                                            <th>Required</th>
                                            <th>Description</th>
                                            <th>Example</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>Name</td><td class="text-danger">Yes</td><td>Student's full name</td><td>John Doe</td></tr>
                                        <tr><td>Email</td><td class="text-danger">Yes</td><td>Valid email (unique)</td><td>john@example.com</td></tr>
                                        <tr><td>Registration Number</td><td class="text-danger">Yes</td><td>Unique registration ID</td><td>REG2024001</td></tr>
                                        <tr><td>Department</td><td class="text-danger">Yes</td><td>Must match system records</td><td>Computer Science and Engineering</td></tr>
                                        <tr><td>Programme</td><td class="text-danger">Yes</td><td>Must belong to department</td><td>B.Tech Computer Science</td></tr>
                                        <tr><td>Phone</td><td class="text-success">No</td><td>Contact number</td><td>9876543210</td></tr>
                                        <tr><td>DOB</td><td class="text-success">No</td><td>Date of birth</td><td>2000-01-15</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('students.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Students
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-upload"></i> Import Students
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-lightbulb"></i> Important Notes</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li><strong>Department names must exactly match</strong> the existing departments in the system</li>
                        <li><strong>Programmes must belong to the selected department</strong> - the system will validate this relationship</li>
                        <li><strong>Email addresses and Registration Numbers must be unique</strong> - duplicates will be rejected</li>
                        <li><strong>Empty rows will be automatically skipped</strong> during import</li>
                        <li><strong>All validations are performed server-side</strong> with clear error messages</li>
                        <li><strong>The import runs as a background job</strong> - large files won't timeout</li>
                        <li><strong>Partial success is supported</strong> - valid rows are imported even if some fail</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-question-circle"></i> Need Help?</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        <i class="fas fa-envelope"></i> Contact support for assistance with bulk imports<br>
                        <small class="text-muted">The downloaded template includes an "Instructions" sheet with detailed guidance.</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection