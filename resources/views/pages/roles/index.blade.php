@extends('layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Role'])
    @include('sweetalert::alert')
    @csrf

    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Manage Roles</h6>
                    <a class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createRoleModal">Create New Role</a>
                </div>

                <div class="card-body px-3 pt-3 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="rolesTable" style="width:100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Role Name</th>
                                    <th>Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>
                                            @if ($role->permissions->count() > 0)
                                                <ul>
                                                    @foreach ($role->permissions as $permission)
                                                        <li>{{ $permission->name }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted">No permissions assigned</span>
                                            @endif
                                        </td>

                                        <td>
                                            <a class="btn btn-primary btn-sm" href="javascript:void(0)"
                                                data-bs-target="#editRoleModal"
                                                onclick="editRole({{ $role->id }})">Edit</a>


                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                style="display:inline;" id="delete-form-{{ $role->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="confirmDelete({{ $role->id }})">Delete</button>
                                            </form>
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

    <!-- Modal for creating a new role -->
    <div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createRoleModalLabel">Create New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('roles.store') }}" method="POST" id="createRoleForm">
                        @csrf
                        <div class="mb-3">
                            <label for="role_name" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="role_name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="permissions" class="form-label">Permissions</label>
                            <div class="row">
                                @foreach ($permissions as $permission)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input"
                                                id="permission_{{ $permission->id }}" name="permissions[]"
                                                value="{{ $permission->id }}">
                                            <label class="form-check-label"
                                                for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editRoleForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Role Name -->
                        <div class="mb-4">
                            <label for="roleName" class="form-label">Role Name</label>
                            <input type="text" name="name" id="roleName"
                                class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hidden Role ID for the form submission -->
                        <input type="hidden" id="roleId" name="id">

                        <!-- Permissions (placed inline) -->
                        <div class="mb-3" id="editPermissions">
                            <label for="permissions" class="form-label">Permissions</label>
                            <div class="d-flex flex-wrap">
                                @foreach ($permissions as $permission)
                                    <div class="form-check me-3">
                                        <input type="checkbox" class="form-check-input"
                                            id="permission_{{ $permission->id }}" name="permissions[]"
                                            value="{{ $permission->id }}">
                                        <label class="form-check-label"
                                            for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @error('permissions')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




    <script>
        function confirmDelete(roleId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + roleId).submit();
                }
            });
        }

        $(document).ready(function() {

            $('#rolesTable').DataTable({
                responsive: true,
            });


            window.editRole = function(id) {
                $.ajax({
                    url: `/user-management/roles/${id}/edit`,
                    method: 'GET',
                    success: function(data) {
                        console.log(data);


                        $('#roleName').val(data.role.name);
                        $('#roleId').val(data.role.id);


                        const assignedPermissionIds = data.role.permissions.map(permission =>
                            permission.id);


                        if (Array.isArray(data.permissions)) {

                            $('#editPermissions').html(data.permissions.map(function(permission) {

                                const isChecked = assignedPermissionIds.includes(
                                    permission.id) ? 'checked' : '';

                                return `
                                <div class="form-check">
                                    <input type="checkbox" name="permissions[]" value="${permission.id}"
                                        id="permission_${permission.id}" class="form-check-input"
                                        ${isChecked}>
                                    <label for="permission_${permission.id}" class="form-check-label">
                                        ${permission.name}
                                    </label>
                                </div>
                            `;
                            }).join(''));
                        } else {
                            console.error('data.permissions tidak ditemukan atau bukan array');
                        }


                        $('#editRoleModal').modal('show');
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to load role data.',
                            text: 'There was an issue with loading the role details.'
                        });
                    }
                });
            };
            $('#editRoleForm').submit(function(e) {
                e.preventDefault();


                var roleId = $('#roleId').val();

                $.ajax({
                    url: `/user-management/roles/${roleId}`,
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#editRoleModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Role updated successfully!',
                            text: response
                                .message
                        });
                        location.reload();
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to update role!',
                            text: 'There was an issue updating the role.'
                        });
                    }
                });
            });
        });
    </script>
@endsection
