@extends('layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Role'])
    @include('sweetalert::alert')

    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Manage Permissions</h6>
                    <a class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createPermissionModal">Add
                        Permission</a>
                </div>

                <div class="card-body px-3 pt-3 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="permissionsTable" style="width:100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="permissionsBody">
                                @foreach ($permissions as $permission)
                                    <tr id="permissionRow_{{ $permission->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $permission->name }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm"
                                                onclick="editPermission({{ $permission->id }})">Edit</button>
                                            <button class="btn btn-danger btn-sm"
                                                onclick="deletePermission({{ $permission->id }})">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Permission Modal -->
    <div class="modal fade" id="createPermissionModal" tabindex="-1" aria-labelledby="createPermissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="createPermissionForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Permission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="permissionName" class="form-label">Permission Name</label>
                            <input type="text" class="form-control" id="permissionName" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Permission Modal -->
    <div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="editPermissionForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Permission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editPermissionName" class="form-label">Permission Name</label>
                            <input type="text" class="form-control" id="editPermissionName" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $('#permissionsTable').DataTable({
                responsive: true,
                "pageLength": 5,
                "lengthMenu": [5, 10, 25, 50, 75, 100]
            });



            // Reset form on modal close
            $('#createPermissionModal, #editPermissionModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
            });

            // Create Permission
            $('#createPermissionForm').on('submit', function(e) {
                e.preventDefault();
                let name = $('#permissionName').val();
                if (!name.trim()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Permission name cannot be empty!'
                    });
                    return;
                }

                $.ajax({
                    url: '{{ route('permissions.store') }}',
                    method: 'POST',
                    data: {
                        name: name,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#permissionsBody').append(`
                        <tr id="permissionRow_${response.data.id}">
                            <td>${$('#permissionsTable tbody tr').length + 1}</td>
                            <td>${response.data.name}</td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="editPermission(${response.data.id})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deletePermission(${response.data.id})">Delete</button>
                            </td>
                        </tr>
                    `);
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Permission successfully added!'
                            });
                            $('#createPermissionModal').modal('hide');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'Failed to add permission.'
                            });
                        }
                    }
                });
            });

            // Edit Permission
            window.editPermission = function(id) {
                $.get(`/user-management/permission/${id}/edit`, function(data) {
                    $('#editPermissionName').val(data.name);
                    $('#editPermissionForm').off('submit').on('submit', function(e) {
                        e.preventDefault();
                        let name = $('#editPermissionName').val();
                        if (!name.trim()) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Permission name cannot be empty!'
                            });
                            return;
                        }

                        $.ajax({
                            url: `/user-management/permission/${id}`,
                            method: 'PUT',
                            data: {
                                name: name,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    $(`#permissionRow_${id} td:nth-child(2)`).text(
                                        name);
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Updated',
                                        text: 'Permission successfully updated!'
                                    });
                                    $('#editPermissionModal').modal('hide');
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed',
                                        text: 'Failed to update permission.'
                                    });
                                }
                            }
                        });
                    });
                    $('#editPermissionModal').modal('show');
                });
            };

            // Delete Permission
            window.deletePermission = function(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/user-management/permission/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    $(`#permissionRow_${id}`).remove();
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted',
                                        text: 'Permission successfully deleted!'
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed',
                                        text: 'Failed to delete permission.'
                                    });
                                }
                            }
                        });
                    }
                });
            };
        });
    </script>
@endsection
