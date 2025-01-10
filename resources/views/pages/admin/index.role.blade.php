@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'User Management'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Roles</h6>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                        Tambah
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table id="rolesTable" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Role Name</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $index => $role)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td class="text-center">
                                            <div class="d-flex flex-column flex-md-row gap-1 justify-content-center">
                                                <button type="button" class="btn btn-link text-info"
                                                    onclick="showPermissions({{ $role->id }})">Permissions</button>
                                                <button type="button" class="btn btn-link text-primary"
                                                    data-bs-toggle="modal" data-bs-target="#editRoleModal"
                                                    data-name="{{ $role->name }}"
                                                    data-id="{{ $role->id }}">Edit</button>
                                                <button type="button" class="btn btn-link text-danger"
                                                    data-bs-toggle="modal" data-bs-target="#deleteRoleModal"
                                                    data-id="{{ $role->id }}">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <script>
                            $(document).ready(function() {
                                $('#rolesTable').DataTable({
                                    // Sesuaikan dengan opsi DataTables yang diinginkan
                                    responsive: true,
                                    scrollX: true,
                                    pageLength: 5,
                                    pagingType: 'simple_numbers',
                                    searching: false,
                                    info: false
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>

        </div>
