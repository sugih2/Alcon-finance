@extends('layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'User'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Users</h6>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        Tambah
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="container">
                        <div class="table-responsive p-0">


                            <table class="table align-items-center mb-0" id="employeeTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            No</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Username</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            First Name</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Last Name</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Email</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Role</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Status</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index = 1; @endphp
                                    @foreach ($users as $user)
                                        <tr>
                                            <td class="align-middle text-center">{{ $index++ }}</td>
                                            <td>{{ $user['username'] }}</td>
                                            <td>{{ $user['firstname'] }}</td>
                                            <td>{{ $user['lastname'] }}</td>
                                            <td>{{ $user['email'] }}</td>
                                            <td>
                                                @foreach ($user['roles'] as $role)
                                                    <span class="badge bg-primary">{{ $role['name'] }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="statusSwitch{{ $user['id'] }}" data-id="{{ $user['id'] }}"
                                                        {{ $user['status'] == 'active' ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="align-middle text-end">
                                                <!-- Tombol Edit -->
                                                <button class="btn btn-link text-primary mb-0" data-bs-toggle="modal"
                                                    data-bs-target="#editUserModal"
                                                    onclick="fillEditUserModal({{ $user['id'] }}, '{{ $user['username'] }}', '{{ $user['firstname'] }}', '{{ $user['lastname'] }}', '{{ $user['email'] }}')">Edit</button>

                                                <!-- Tombol Delete -->
                                                <form action="{{ route('users.destroy', $user['id']) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger mb-0"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Delete</button>
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
    </div>




    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Tambah Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="firstname" class="form-label">Firstname</label>
                            <input type="text" class="form-control" id="firstname" name="firstname"
                                placeholder="Firstname" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Lastname</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Lastname"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="example@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal untuk Edit User -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_user_id" name="user_id">
                        <div class="form-group">
                            <label for="edit_firstname">Nama Depan</label>
                            <input type="text" class="form-control" id="edit_firstname" name="firstname" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_lastname">Nama Belakang</label>
                            <input type="text" class="form-control" id="edit_lastname" name="lastname" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_email">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_role">Role</label>
                            <select class="form-control" id="edit_role" name="role" required>
                                <option value="">Pilih Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_password">Password</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#addUserForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route('users.store') }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({
                        title: 'Sukses!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#users-table').DataTable().ajax.reload();
                        $('#addUserModal').modal('hide');
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Terjadi Kesalahan!',
                        text: 'Terjadi kesalahan: ' + xhr.responseJSON.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Edit User
        $(document).on('click', '.edit-user', function() {
            var user_id = $(this).data('id');

            $.get('{{ url('user-management/users') }}/' + user_id, function(data) {
                $('#edit_user_id').val(data.id);
                $('#edit_firstname').val(data.firstname);
                $('#edit_lastname').val(data.lastname);
                $('#edit_email').val(data.email);
                $('#edit_role').val(data.role_name);
                $('#editUserModal').modal('show');
            });
        });


        $(document).on('click', '.edit-btn', function() {
            var userId = $(this).data('id');
            fillEditUserModal(userId);
        });


        function fillEditUserModal(userId) {
            $.ajax({
                url: '/user-management/users/' + userId,
                method: 'GET',
                success: function(response) {

                    $('#edit_user_id').val(response.id);
                    $('#edit_firstname').val(response.firstname);
                    $('#edit_lastname').val(response.lastname);
                    $('#edit_email').val(response.email);
                    $('#edit_role').val(response.role);
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Terjadi Kesalahan!',
                        text: 'Terjadi kesalahan saat mengambil data user!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        $('#editUserForm').submit(function(e) {
            e.preventDefault();

            var formData = $(this).serialize();


            formData += '&_method=PUT';


            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            $.ajax({
                url: '/user-management/users/' + $('#edit_user_id').val(),
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Sukses!',
                        text: 'User berhasil diperbarui!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#editUserModal').modal('hide');
                        location
                            .reload();
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Terjadi Kesalahan!',
                        text: 'Terjadi kesalahan saat memperbarui user!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });


        $(document).on('click', '.delete-user', function() {
            var user_id = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'User ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url('user-management/users') }}/' + user_id,
                        method: 'DELETE',
                        success: function(response) {
                            Swal.fire({
                                title: 'Sukses!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                $('#users-table').DataTable().ajax.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Terjadi Kesalahan!',
                                text: 'Terjadi kesalahan: ' + xhr.responseJSON.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
