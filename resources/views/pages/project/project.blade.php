@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Project'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6></h6>
                    <button type="button" class="btn btn-primary" onclick="createProject()">
                        Tambah Project
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="container">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            No
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Name</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Code</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Jenis</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Deskripsi</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Daerah/Kota</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index = 1; @endphp
                                    @foreach ($projects as $p)
                                        <tr>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $index++ }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $p->name }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $p->code }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $p->jenis }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $p->description }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $p->regency->name }}</p>
                                            </td>
                                            <td class="align-middle text-end">
                                                <div class="d-flex px-3 py-1 justify-content-center align-items-center">
                                                    <button type="button" class="btn btn-link text-primary mb-0"
                                                        onclick="editProject({{ $p->id }})">
                                                        Edit
                                                    </button>
                                                    <button type="button" class="btn btn-link text-danger mb-0"
                                                        data-bs-toggle="modal" data-bs-target="#deleteRoleModal"
                                                        data-id="{{ $p->id }}">
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
        </div>
    </div>
    <div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProjectModalLabel">Tambah Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="createProject"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="EditProjectModal" tabindex="-1" aria-labelledby="EditProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="EditProjectModalLabel">Tambah Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editProject"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function createProject() {
            $.ajax({
                url: "{{ url('/project/create') }}",
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    $("#createProject").html(data);
                    $('#addProjectModal').modal('show');
                    $(document).ready(function() {
                        $('#regency').selectize({
                            placeholder: 'Select Daerah/Kota',
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            load: function(query, callback) {
                                if (!query.length) return callback();
                                $.ajax({
                                    url: '/regency',
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
                        text: 'Failed to open create project form. Please try again later.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        function editProject(id) {
            $.ajax({
                url: "{{ url('/project/edit') }}/" + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $("#editProject").html(response.html);
                    $('#EditProjectModal').modal('show');

                    const existingRegencyId = response.regency_id;

                    $('#regencyedit').selectize({
                        placeholder: 'Select Daerah/Kota',
                        valueField: 'id',
                        labelField: 'name',
                        searchField: 'name',
                        preload: true,

                        load: function(query, callback) {
                            $.ajax({
                                url: '/regency',
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
                                        text: 'Failed to load Regency data. Please try again later.',
                                        confirmButtonText: 'OK'
                                    });
                                    callback();
                                }
                            });
                        },
                        onInitialize: function() {
                            const selectize = this;

                            if (existingRegencyId) {
                                $.ajax({
                                    url: '/regency/get-regency-name',
                                    type: 'GET',
                                    dataType: 'json',
                                    data: {
                                        regency_id: existingRegencyId
                                    },
                                    success: function(data) {
                                        selectize.addOption({
                                            id: existingRegencyId,
                                            name: data.name
                                        });
                                        selectize.setValue(existingRegencyId);
                                    }
                                });
                            }
                        }
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to open create position form. Please try again later.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        async function StoreProject() {
            event.preventDefault();

            const form = document.getElementById('FromProject');
            const formData = new FormData(form);
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/project/store', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

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
            }
        }

        async function StoreEditProjects(id) {
            event.preventDefault();

            const form = document.getElementById('FromEditprojects');
            const formData = new FormData(form);
            const submitButton = document.getElementById('btn-submit');

            submitButton.disabled = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/project/storeedit/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

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
    </script>
@endsection
