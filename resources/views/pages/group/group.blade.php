@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Group'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6></h6>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGroupModal">
                        Tambah Group
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Code</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Project</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Kepala Group</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $index = 1; @endphp
                                @foreach ($groups as $g)
                                    <tr>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $index++ }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $g->name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $g->code }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $g->project->name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $g->leader->name }}</p>
                                        </td>

                                        <td class="align-middle text-end">
                                            <div class="d-flex px-3 py-1 justify-content-center align-items-center">
                                                <button type="button" class="btn btn-link text-primary mb-0"
                                                    data-bs-toggle="modal" data-bs-target="#editRoleModal"
                                                    data-name="{{ $g->name }}" data-id="{{ $g->id }}">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-link text-danger mb-0"
                                                    data-bs-toggle="modal" data-bs-target="#deleteRoleModal"
                                                    data-id="{{ $g->id }}">
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
    <div class="modal fade" id="addGroupModal" tabindex="-1" aria-labelledby="addGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGroupModalLabel">Tambah Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form id="FormGroup">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name Group</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control" id="code" name="code" placeholder="code"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="project" class="form-label">Project</label>
                            <select name="project" id="project" required>
                                <option value="" selected>Select Project</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="leader" class="form-label">Leader</label>
                            <select name="leader" id="leader" required>
                                <option value="" selected>Select Leader</option>
                            </select>
                        </div>
                        <button type="button" id="btn-submit" onclick="StoreGroup()"
                            class="btn btn-primary">Simpan</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#project').selectize({
                placeholder: 'Select Project',
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                preload: true,
                load: function(query, callback) {

                    $.ajax({
                        url: '/project/list',
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
            $('#leader').selectize({
                placeholder: 'Select Leader',
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                preload: true,
                load: function(query, callback) {
                    $.ajax({
                        url: '/employee/list',
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


        async function StoreGroup() {
            event.preventDefault();

            const form = document.getElementById('FormGroup');
            const formData = new FormData(form);
            const submitButton = document.getElementById('btn-submit'); // Get the button element

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
                const response = await fetch('/group/store', {
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
    </script>
@endsection
