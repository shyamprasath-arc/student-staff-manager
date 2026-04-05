@extends('layouts.app')

@section('title', 'Staff Management')

@section('content')
<div>
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2><i class="fas fa-chalkboard-user"></i> Staff Management</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
            <i class="fas fa-plus"></i> Add New Staff
        </button>
    </div>

    <div class="card">
        <div class="card-body p-0 p-md-3">
            <div class="table-responsive">
                <table class="table table-hover" id="staff-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Employee ID</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addStaffModalLabel"><i class="fas fa-plus-circle"></i> Add New Staff Member</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addStaffForm">
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
                            <label for="employee_id" class="form-label">Employee ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="employee_id" name="employee_id" required>
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
                            <label for="designation" class="form-label">Designation <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="designation" name="designation" required placeholder="e.g., Professor, HOD, Assistant Professor">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveStaffBtn">
                    <i class="fas fa-save"></i> Save Staff
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#staff-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("staff.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'employee_id', name: 'employee_id' },
            { data: 'department_name', name: 'department_name' },
            { data: 'designation', name: 'designation' },
            { data: 'phone', name: 'phone' }
        ],
        order: [[0, 'desc']],
        responsive: true,
        scrollX: true
    });

    $('#saveStaffBtn').click(function() {
        var submitBtn = $(this);
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: '{{ route("staff.store") }}',
            type: 'POST',
            data: $('#addStaffForm').serialize(),
            success: function(response) {
                if(response.success) {
                    $('#addStaffModal').modal('hide');
                    $('#addStaffForm')[0].reset();
                    table.ajax.reload();
                    showAlert('success', response.message);
                    $('.invalid-feedback').empty();
                    $('.form-control').removeClass('is-invalid');
                }
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Staff');
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
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Staff');
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