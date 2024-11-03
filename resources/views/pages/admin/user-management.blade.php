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
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Role Name</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $index = 1; @endphp
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $index++ }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $role->name }}</p>
                                        </td>
                                        <td class="align-middle text-end">
                                            <div class="d-flex px-3 py-1 justify-content-center align-items-center">
                                                <button type="button" class="btn btn-link text-primary mb-0"
                                                    data-bs-toggle="modal" data-bs-target="#editRoleModal"
                                                    data-name="{{ $role->name }}" data-id="{{ $role->id }}">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-link text-danger mb-0"
                                                    data-bs-toggle="modal" data-bs-target="#deleteRoleModal"
                                                    data-id="{{ $role->id }}">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Menu</h6>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMenuModal">
                        Tambah
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="menuTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Nama Menu</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Url</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $index = 1; @endphp
                                @foreach ($menus as $menu)
                                    <tr>
                                        <td class="align-middle text-center">
                                            <p class="text-sm font-weight-bold mb-0">{{ $index++ }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $menu->name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $menu->url }}</p>
                                        </td>
                                        <td class="align-middle text-end">
                                            <div class="d-flex px-3 py-1 justify-content-center align-items-center">
                                                <button type="button" class="btn btn-link text-primary mb-0"
                                                    data-bs-toggle="modal" data-bs-target="#editMenuModal"
                                                    data-name="{{ $menu->name }}" data-id="{{ $menu->id }}">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-link text-danger mb-0"
                                                    data-bs-toggle="modal" data-bs-target="#deleteMenuModal"
                                                    data-id="{{ $menu->id }}">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Users</h6>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        Tambah
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Role</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Create Date</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-3 py-1">
                                                <div>
                                                    <img src="./img/team-{{ $user->id }}.jpg" class="avatar me-3"
                                                        alt="image">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $user->firstname }} {{ $user->lastname }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $user->role->name }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-sm font-weight-bold mb-0">
                                                {{ $user->created_at->format('d/m/Y') }}</p>
                                        </td>
                                        <td class="align-middle text-end">
                                            <div class="d-flex px-3 py-1 justify-content-center align-items-center">
                                                <button class="btn btn-link text-primary mb-0" data-bs-toggle="modal"
                                                    data-bs-target="#editUserModal"
                                                    onclick="fillEditUserModal({{ $user }})">Edit</button>
                                                <button class="btn btn-link text-danger mb-0" data-bs-toggle="modal"
                                                    data-bs-target="#deleteUserModal"
                                                    onclick="setDeleteFormAction('{{ route('users.destroy', $user) }}')">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Users</h6>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        Tambah
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table id="usersTable" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Role</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Create Date</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            $(document).ready(function() {
            $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('users.data') }}', // Pastikan ini benar
                columns: [
                    {
                        data: 'name',
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex px-3 py-1">
                                    <div>
                                        <img src="./img/team-${row.id}.jpg" class="avatar me-3" alt="image">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">${row.firstname} ${row.lastname}</h6>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    { data: 'role.name', name: 'role.name' },
                    { 
                        data: 'created_at', 
                        name: 'created_at',
                        render: function(data) {
                            return moment(data).format('DD/MM/YYYY'); // Pastikan moment.js sudah diimpor
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex px-3 py-1 justify-content-center align-items-center">
                                    <button class="btn btn-link text-primary mb-0" data-bs-toggle="modal"
                                        data-bs-target="#editUserModal"
                                        onclick="fillEditUserModal(${JSON.stringify(row)})">Edit</button>
                                    <button class="btn btn-link text-danger mb-0" data-bs-toggle="modal"
                                        data-bs-target="#deleteUserModal"
                                        onclick="setDeleteFormAction('${row.delete_url}')">
                                        Delete
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                language: {
                    processing: '<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>'
                }
            });
        });
        </script> --}}
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
                            <input type="text" class="form-control" id="lastname" name="lastname"
                                placeholder="Lastname" required>
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
                            <label for="role_id" class="form-label">Role</label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm" action="#" method="POST">
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
                            <select class="form-select" id="edit_role_id" name="role_id" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus pengguna ini?
                </div>
                <div class="modal-footer">
                    <form id="delete-form" action="" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoleModalLabel">Tambah Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addRoleForm" action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="addRoleName" name="name"
                                placeholder="Role Name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('roles.update', 'role_id_placeholder') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="role-name" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="role-name" name="name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteRoleModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus role ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteRoleForm" action="#" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addMenuModal" tabindex="-1" aria-labelledby="addMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMenuModalLabel">Tambah Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="menuForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Menu</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="url" class="form-label">URL</label>
                            <input type="text" class="form-control" id="url" name="url" required>
                        </div>
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Parent ID</label>
                            <input type="text" class="form-control" id="parent_id" name="parent_id">
                        </div>
                        <div class="mb-3">
                            <label for="urutan" class="form-label">Urutan</label>
                            <input type="number" class="form-control" id="urutan" name="urutan" required>
                        </div>
                        <div class="mb-3">
                            <label for="icon" class="form-label">Ikon</label>
                            <input type="text" class="form-control" id="icon" name="icon">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editMenuModal" tabindex="-1" aria-labelledby="editMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMenuModalLabel">Edit Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editMenuForm">
                        <input type="hidden" id="editMenuId" name="id">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Nama Menu</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editUrl" class="form-label">URL</label>
                            <input type="text" class="form-control" id="editUrl" name="url" required>
                        </div>
                        <div class="mb-3">
                            <label for="editParentId" class="form-label">Parent ID</label>
                            <input type="text" class="form-control" id="editParentId" name="parent_id">
                        </div>
                        <div class="mb-3">
                            <label for="editUrutan" class="form-label">Urutan</label>
                            <input type="number" class="form-control" id="editUrutan" name="urutan" required>
                        </div>
                        <div class="mb-3">
                            <label for="editIcon" class="form-label">Ikon</label>
                            <input type="text" class="form-control" id="editIcon" name="icon">
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteMenuModal" tabindex="-1" aria-labelledby="deleteMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteMenuModalLabel">Konfirmasi Hapus Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus menu ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="confirmDeleteButton" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </div>
    </div>        
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('menuForm');
            const editForm = document.getElementById('editMenuForm');
            const tableBody = document.querySelector('#menuTable tbody');
            const deleteButton = document.getElementById('confirmDeleteButton');
            let deleteMenuId;
    
            // Event listener untuk submit form menambah menu
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Mencegah reload halaman
    
                const formData = new FormData(form); // Mengambil data dari formulir
    
                fetch('/user-management/menus', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Sertakan token CSRF
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Tambahkan baris baru ke tabel
                    const row = `
                        <tr>
                            <td class="align-middle text-center">
                                <p class="text-sm font-weight-bold mb-0">${tableBody.children.length + 1}</p>
                            </td>
                            <td>
                                <p class="text-sm font-weight-bold mb-0">${data.name}</p>
                            </td>
                            <td>
                                <p class="text-sm font-weight-bold mb-0">${data.url}</p>
                            </td>
                            <td class="align-middle text-end">
                                <div class="d-flex px-3 py-1 justify-content-center align-items-center">
                                    <button type="button" class="btn btn-link text-primary mb-0"
                                        data-bs-toggle="modal" data-bs-target="#editMenuModal"
                                        data-name="${data.name}" data-id="${data.id}">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-link text-danger mb-0"
                                        data-bs-toggle="modal" data-bs-target="#deleteMenuModal"
                                        data-id="${data.id}">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                    // Tutup modal dan reset form
                    $('#addMenuModal').modal('hide');
                    form.reset();
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
    
            // Fetch dan render data menu
            fetchMenuData();
    
            function fetchMenuData() {
                fetch('/user-management/menus')
                    .then(response => response.json())
                    .then(data => {
                        window.menuData = data;
                        renderMenuTable();
                    })
                    .catch(error => {
                        console.error('Error fetching menu data:', error);
                    });
            }
    
            function renderMenuTable() {
                tableBody.innerHTML = ''; // Clear the table body
    
                window.menuData.forEach((menu, index) => {
                    const row = `
                        <tr>
                            <td class="align-middle text-center">
                                <p class="text-sm font-weight-bold mb-0">${index + 1}</p>
                            </td>
                            <td>
                                <p class="text-sm font-weight-bold mb-0">${menu.name}</p>
                            </td>
                            <td>
                                <p class="text-sm font-weight-bold mb-0">${menu.url}</p>
                            </td>
                            <td class="align-middle text-end">
                                <div class="d-flex px-3 py-1 justify-content-center align-items-center">
                                    <button type="button" class="btn btn-link text-primary mb-0"
                                        data-bs-toggle="modal" data-bs-target="#editMenuModal"
                                        data-id="${menu.id}">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-link text-danger mb-0"
                                        data-bs-toggle="modal" data-bs-target="#deleteMenuModal"
                                        data-id="${menu.id}">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
    
                // Set event listeners untuk tombol edit dan delete setelah tabel dirender
                setEditButtonListeners();
                setDeleteButtonListeners();
            }
    
            function setEditButtonListeners() {
                document.querySelectorAll('.btn-link.text-primary').forEach(button => {
                    button.addEventListener('click', function() {
                        const menuId = this.getAttribute('data-id');
                        fetch(`/user-management/menus/${menuId}`)
                            .then(response => response.json())
                            .then(data => {
                                document.getElementById('editMenuId').value = data.id;
                                document.getElementById('editName').value = data.name;
                                document.getElementById('editUrl').value = data.url;
                                document.getElementById('editParentId').value = data.parent_id || '';
                                document.getElementById('editUrutan').value = data.urutan || '';
                                document.getElementById('editIcon').value = data.icon || '';
                                document.getElementById('editStatus').value = data.status ? 1 : 0;
                                $('#editMenuModal').modal('show');
                            });
                    });
                });
            }
    
            // Event listener untuk submit form edit
            editForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Mencegah reload halaman
    
                const formData = new FormData(editForm);
                const menuId = document.getElementById('editMenuId').value;
    
                fetch(`/user-management/menus/${menuId}`, {
                    method: 'PUT',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', // Sertakan token CSRF
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(JSON.stringify(err));
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Update tabel dengan data baru
                    fetchMenuData();
                    $('#editMenuModal').modal('hide');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
    
            function setDeleteButtonListeners() {
                document.querySelectorAll('.btn-link.text-danger').forEach(button => {
                    button.addEventListener('click', function() {
                        deleteMenuId = this.getAttribute('data-id');
                        $('#deleteMenuModal').modal('show');
                    });
                });
            }
    
            // Event listener untuk konfirmasi hapus
            deleteButton.addEventListener('click', function() {
                fetch(`/user-management/menus/${deleteMenuId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Sertakan token CSRF
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update tabel setelah penghapusan
                    fetchMenuData();
                    $('#deleteMenuModal').modal('hide');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    </script>             
    <script>
        function fillEditUserModal(user) {
            document.getElementById('editUserForm').action =
                `/user-management/users/${user.id}`; // Ubah URL sesuai rute Anda
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_firstname').value = user.firstname;
            document.getElementById('edit_lastname').value = user.lastname;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_role_id').value = user.role_id;
        }

        function setDeleteFormAction(action) {
            document.getElementById('delete-form').action = action;
        }
        var editRoleModal = document.getElementById('editRoleModal');
        editRoleModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget; // Tombol yang mengaktifkan modal
            var roleName = button.getAttribute('data-name');
            var roleId = button.getAttribute('data-id');

            var modalTitle = editRoleModal.querySelector('.modal-title');
            var roleInput = editRoleModal.querySelector('#role-name');
            var form = editRoleModal.querySelector('form');

            modalTitle.textContent = 'Edit Role';
            roleInput.value = roleName;
            form.action = '{{ url('user-management/roles') }}' + '/' + roleId; // Ganti action form
        });
        var deleteRoleModal = document.getElementById('deleteRoleModal');
        deleteRoleModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget; // Tombol yang mengaktifkan modal
            var roleId = button.getAttribute('data-id');

            var form = deleteRoleModal.querySelector('#deleteRoleForm');
            form.action = '{{ url('user-management/roles') }}/' + roleId; // Ganti action form
        });
    </script>
@endsection
