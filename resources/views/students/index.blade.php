@extends('layouts.app')

@section('title', 'Student Management')

@section('content')
<div>
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2><i class="fas fa-user-graduate"></i> Student Management</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
            <i class="fas fa-plus"></i> Add New Student
        </button>
    </div>

    <div class="card">
        <div class="card-body p-0 p-md-3">
            <div class="table-responsive">
                <table class="table table-hover" id="students-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registration No</th>
                            <th>Department</th>
                            <th>Programme</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addStudentModalLabel"><i class="fas fa-plus-circle"></i> Add New Student</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addStudentForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="registration_number" class="form-label">Registration Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="registration_number" name="registration_number" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-control" id="department_id" name="department_id" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="programme_id" class="form-label">Programme <span class="text-danger">*</span></label>
                            <select class="form-control" id="programme_id" name="programme_id" required disabled>
                                <option value="">First select department</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveStudentBtn">
                    <i class="fas fa-save"></i> Save Student
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#students-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("students.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'registration_number', name: 'registration_number' },
            { data: 'department_name', name: 'department_name' },
            { data: 'programme_name', name: 'programme_name' },
            { data: 'phone', name: 'phone' }
        ],
        order: [[0, 'desc']],
        responsive: true,
        scrollX: true
    });

    // Department change - load programmes
    $('#department_id').change(function() {
        var deptId = $(this).val();
        var programmeSelect = $('#programme_id');
        
        if(deptId) {
            programmeSelect.prop('disabled', true).html('<option value="">Loading...</option>');
            
            $.ajax({
                url: '/get-programmes/' + deptId,
                type: 'GET',
                success: function(data) {
                    programmeSelect.prop('disabled', false).empty();
                    programmeSelect.append('<option value="">Select Programme</option>');
                    
                    if(data.length === 0) {
                        programmeSelect.append('<option value="">No programmes found</option>');
                    } else {
                        $.each(data, function(key, programme) {
                            programmeSelect.append('<option value="'+ programme.id +'">'+ programme.name +' ('+ programme.code +')</option>');
                        });
                    }
                },
                error: function() {
                    programmeSelect.prop('disabled', false).html('<option value="">Error loading programmes</option>');
                }
            });
        } else {
            programmeSelect.prop('disabled', true).html('<option value="">First select department</option>');
        }
    });

    // Save student via AJAX
    $('#saveStudentBtn').click(function() {
        var formData = $('#addStudentForm').serialize();
        var submitBtn = $(this);
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: '{{ route("students.store") }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    $('#addStudentModal').modal('hide');
                    $('#addStudentForm')[0].reset();
                    $('#programme_id').prop('disabled', true).html('<option value="">First select department</option>');
                    table.ajax.reload();
                    showAlert('success', response.message);
                    $('.invalid-feedback').empty();
                    $('.form-control').removeClass('is-invalid');
                }
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Student');
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $('.invalid-feedback').empty();
                    $('.form-control').removeClass('is-invalid');
                    
                    $.each(errors, function(key, value) {
                        $('#'+key).addClass('is-invalid');
                        $('#'+key).siblings('.invalid-feedback').text(value[0]);
                    });
                } else {
                    showAlert('danger', 'An error occurred. Please try again.');
                }
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Student');
            }
        });
    });

    function showAlert(type, message) {
        var alertDiv = $('<div class="alert alert-'+type+' alert-dismissible fade show" role="alert">'+
            '<i class="fas fa-'+(type === 'success' ? 'check-circle' : 'exclamation-circle')+'"></i> '+
            message+
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
        $('.content-inner').prepend(alertDiv);
        setTimeout(function() { alertDiv.fadeOut('slow'); }, 5000);
    }
});
</script>
@endpush