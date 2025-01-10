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

                            {{-- <table class="table align-items-center mb-0" id="employeeTable" style="width:100%">
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
                                                <button class="btn btn-link text-primary mb-0" data-bs-toggle="modal"
                                                    data-bs-target="#editUserModal"
                                                    onclick="fillEditUserModal({{ $user['id'] }}, '{{ $user['username'] }}', '{{ $user['firstname'] }}', '{{ $user['lastname'] }}', '{{ $user['email'] }}')">Edit</button>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table> --}}
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

    {{-- <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Pastikan $user yang diteruskan ke view berisi data yang benar -->
                    <form id="editUserForm" action="{{ route('users.update', ['user' => $user->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" name="username"
                                value="{{ $user->username }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_firstname" class="form-label">Firstname</label>
                            <input type="text" class="form-control" id="edit_firstname" name="firstname"
                                value="{{ $user->firstname }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_lastname" class="form-label">Lastname</label>
                            <input type="text" class="form-control" id="edit_lastname" name="lastname"
                                value="{{ $user->lastname }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email"
                                value="{{ $user->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Password (Kosongkan jika tidak diubah)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="edit_role_id" class="form-label">Role</label>
                            <select class="form-select" id="edit_role_id" name="role" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}" @if (in_array($role->name, $user->getRoleNames()->toArray())) selected @endif>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form untuk Edit Pengguna -->
                    <form id="editUserForm" action="{{ route('users.update', ['user' => ':userId']) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_firstname" class="form-label">Firstname</label>
                            <input type="text" class="form-control" id="edit_firstname" name="firstname" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_lastname" class="form-label">Lastname</label>
                            <input type="text" class="form-control" id="edit_lastname" name="lastname" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Password (Kosongkan jika tidak diubah)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="edit_role_id" class="form-label">Role</label>
                            <select class="form-select" id="edit_role_id" name="role" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#employeeTable').DataTable({
                responsive: true,
            });
        });

        function createEmployee() {
            $.ajax({
                url: "{{ url('/employee/create') }}",
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    $("#createEmployee").html(data);
                    $('#addEmployeeModal').modal('show');
                    $(document).ready(function() {
                        $('#position').selectize({
                            placeholder: 'Select Position',
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            preload: true,
                            load: function(query, callback) {
                                $.ajax({
                                    url: '/position/list',
                                    type: 'GET',
                                    dataType: 'json',
                                    data: {
                                        q: query
                                    },
                                    success: function(data) {
                                        callback(data);
                                    },
                                    error: function() {
                                        callback();
                                    }
                                });
                            }
                        });
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to open create Employee form. Please try again later.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        function editEmployee(id) {
            if ($.fn.DataTable.isDataTable('#employeeTable')) {
                $('#employeeTable').DataTable().destroy();
            }
            $.ajax({
                url: "{{ url('/employee/edit') }}/" + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $("#editEmployee").html(response.html);
                    $('#EditEmployeeModal').modal('show');
                    const existingPositionId = response.position_id;
                    $(document).ready(function() {
                        $('#position').selectize({
                            placeholder: 'Select Position',
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            preload: true,
                            load: function(query, callback) {
                                $.ajax({
                                    url: '/position/list',
                                    type: 'GET',
                                    dataType: 'json',
                                    data: {
                                        q: query
                                    },
                                    success: function(data) {
                                        callback(data);
                                    },
                                    error: function() {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Failed to load Position data. Please try again later.',
                                            confirmButtonText: 'OK'
                                        });
                                        callback();
                                    }
                                });
                            },
                            onInitialize: function() {
                                const selectize = this;

                                if (existingPositionId) {
                                    $.ajax({
                                        url: '/position/get-position-name',
                                        type: 'GET',
                                        dataType: 'json',
                                        data: {
                                            parent_id: existingPositionId
                                        },
                                        success: function(data) {
                                            selectize.addOption({
                                                id: existingPositionId,
                                                name: data.name
                                            });
                                            selectize.setValue(
                                                existingPositionId);
                                        }
                                    });
                                }
                            }
                        });
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to open Edit Employee form. Please try again later.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // Add event listener for switch button
        document.querySelectorAll('.form-check-input[role="switch"]').forEach(function(switchInput) {
            switchInput.addEventListener('change', function() {
                const employeeId = this.dataset.id;
                const newStatus = this.checked ? 'Aktif' : 'NonAktif';

                // Update label text
                this.nextElementSibling.textContent = newStatus;

                updateEmployeeStatus(employeeId, newStatus);
            });
        });

        // Function to update employee status
        async function updateEmployeeStatus(id, status) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/employee/update-status/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: status
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal mengubah status.');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message || 'Status berhasil diubah'
                });

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message || 'Gagal mengubah status'
                });
            }
        }

        // Store employee data
        async function StoreEmployee() {
            event.preventDefault();

            const form = document.getElementById('FromEmployee');
            const formData = new FormData(form);
            const submitButton = document.getElementById('btn-submit');

            // Disable the button to prevent double-clicks
            submitButton.disabled = true;
            Swal.fire({
                title: 'Menyimpan data...',
                html: 'Progress: <b>0%</b>',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            console.log('Isi FormData:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 10;
                    Swal.update({
                        html: `Progress: <b>${progress}%</b>`
                    });
                    if (progress >= 90) clearInterval(progressInterval); // Stop updating near completion
                }, 200); // Update every 200ms
                const response = await fetch('/employee/store', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });
                clearInterval(progressInterval);

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Gagal menyimpan data.');
                }

                const data = await response.json();
                console.log('Sukses:', data);

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data berhasil disimpan'
                }).then(() => {
                    location.reload();
                });

                form.reset();

            } catch (error) {
                console.error('Error:', error);

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message || 'Data gagal disimpan'
                });
            } finally {
                submitButton.disabled = false;
            }
        }

        // Store edited employee data
        async function StoreEditEmployee(id) {
            event.preventDefault();

            const form = document.getElementById('FromEditEmployee');
            const formData = new FormData(form);
            const submitButton = document.getElementById('btn-submit');

            submitButton.disabled = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/employee/update/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Gagal Setting Employee.');
                }

                console.log('CEKK', response)

                const data = await response.json();
                console.log('Sukses:', data);

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Setting Employee Berhasil'
                }).then(() => {
                    location.reload();
                });

                form.reset();

            } catch (error) {
                console.error('Error:', error);

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message || 'Setting Employee Gagal'
                });
            } finally {
                submitButton.disabled = false;
            }
        }

        function fillEditUserModal(id, username, firstname, lastname, email) {
            // Mengisi input field dengan data pengguna yang dipilih
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_firstname').value = firstname;
            document.getElementById('edit_lastname').value = lastname;
            document.getElementById('edit_email').value = email;

            // Mengatur action form dengan id pengguna yang dipilih
            let form = document.getElementById('editUserForm');
            form.action = "/users/" + id; // Pastikan ini sesuai dengan route untuk update
        }
    </script>



    {{-- <script>
        $(document).ready(function() {
            $('#employeeTable').DataTable({
                responsive: true,
            });
        });

        function createEmployee() {
            $.ajax({
                url: "{{ url('/employee/create') }}",
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    $("#createEmployee").html(data);
                    $('#addEmployeeModal').modal('show');
                    $(document).ready(function() {
                        $('#position').selectize({
                            placeholder: 'Select Position',
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            preload: true,
                            load: function(query, callback) {
                                $.ajax({
                                    url: '/position/list',
                                    type: 'GET',
                                    dataType: 'json',
                                    data: {
                                        q: query
                                    },
                                    success: function(data) {

                                        callback(data);
                                    },
                                    error: function() {

                                        callback();
                                    }
                                });
                            }
                        });
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to open create Group form. Please try again later.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        function editEmployee(id) {
            if ($.fn.DataTable.isDataTable('#employeeTable')) {
                $('#employeeTable').DataTable().destroy();
            }
            $.ajax({
                url: "{{ url('/employee/edit') }}/" + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $("#editEmployee").html(response.html);
                    $('#EditEmployeeModal').modal('show');
                    const existingPositionId = response.position_id;
                    $(document).ready(function() {
                        $('#position').selectize({
                            placeholder: 'Select Position',
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            preload: true,
                            load: function(query, callback) {
                                $.ajax({
                                    url: '/position/list',
                                    type: 'GET',
                                    dataType: 'json',
                                    data: {
                                        q: query
                                    },
                                    success: function(data) {
                                        console.log('Response data:', data);

                                        callback(data);
                                    },
                                    error: function() {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Failed to load Position data. Please try again later.',
                                            confirmButtonText: 'OK'
                                        });
                                        callback();
                                    }
                                });
                            },
                            onInitialize: function() {
                                const selectize = this;

                                if (existingPositionId) {
                                    $.ajax({
                                        url: '/position/get-position-name',
                                        type: 'GET',
                                        dataType: 'json',
                                        data: {
                                            parent_id: existingPositionId
                                        },
                                        success: function(data) {
                                            selectize.addOption({
                                                id: existingPositionId,
                                                name: data.name
                                            });
                                            selectize.setValue(
                                                existingPositionId);
                                        }
                                    });
                                }
                            }
                        });
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to open Edit Employee form. Please try again later.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // Tambahkan event listener untuk switch button
        document.querySelectorAll('.form-check-input[role="switch"]').forEach(function(switchInput) {
            switchInput.addEventListener('change', function() {
                const employeeId = this.dataset.id;
                const newStatus = this.checked ? 'Aktif' : 'NonAktif';

                // Update label text
                this.nextElementSibling.textContent = newStatus;

                updateEmployeeStatus(employeeId, newStatus);
            });
        });
        // Fungsi untuk update status
        async function updateEmployeeStatus(id, status) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/employee/update-status/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json' // Tambahkan ini
                    },
                    body: JSON.stringify({ // Gunakan JSON.stringify
                        status: status
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal mengubah status.');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message || 'Status berhasil diubah'
                });

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message || 'Gagal mengubah status'
                });
            }
        }
        async function StoreEmployee() {
            event.preventDefault();

            const form = document.getElementById('FromEmployee');
            const formData = new FormData(form);
            const submitButton = document.getElementById('btn-submit');

            // Disable the button to prevent double-clicks
            submitButton.disabled = true;
            Swal.fire({
                title: 'Menyimpan data...',
                html: 'Progress: <b>0%</b>',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            console.log('Isi FormData:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 10;
                    Swal.update({
                        html: `Progress: <b>${progress}%</b>`
                    });
                    if (progress >= 90) clearInterval(
                        progressInterval); // Stop updating near completion
                }, 200); // Update every 200ms
                const response = await fetch('/employee/store', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });
                clearInterval(progressInterval);

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Gagal menyimpan data.');
                }

                const data = await response.json();
                console.log('Sukses:', data);

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data berhasil disimpan'
                }).then(() => {
                    location.reload();
                });

                form.reset();

            } catch (error) {
                console.error('Error:', error);

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message || 'Data gagal disimpan'
                });
            } finally {
                submitButton.disabled = false;
            }
        }

        async function StoreEditEmployee(id) {
            event.preventDefault();

            const form = document.getElementById('FromEditEmployee');
            const formData = new FormData(form);
            const submitButton = document.getElementById('btn-submit');

            submitButton.disabled = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/employee/update/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Gagal Setting Employee.');
                }

                console.log('CEKK', response)

                const data = await response.json();
                console.log('Sukses:', data);

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Setting Employee Berhasil'
                }).then(() => {
                    location.reload();
                });

                form.reset();

            } catch (error) {
                console.error('Error:', error);

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message || 'Setting Employee Gagal'
                });
            } finally {
                submitButton.disabled = false;
            }
        }


        function fillEditUserModal(id, username, firstname, lastname, email) {
            // Mengisi input field dengan data pengguna yang dipilih
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_firstname').value = firstname;
            document.getElementById('edit_lastname').value = lastname;
            document.getElementById('edit_email').value = email;
            // document.getElementById('edit_role_id').value = role;

            // Mengatur action form dengan id pengguna yang dipilih
            let form = document.getElementById('editUserForm');
            form.action = "/users/" + id; // Pastikan ini sesuai dengan route untuk update
        }
    </script> --}}
@endsection
