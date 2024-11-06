@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Project'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6></h6>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                        Tambah Employee
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
                                        NIP</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Position</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $index = 1; @endphp
                                @foreach ($employees as $e)
                                    <tr>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $index++ }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $e->name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $e->nip }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $e->position->name }}</p>
                                        </td>

                                        <td class="align-middle text-end">
                                            <div class="d-flex px-3 py-1 justify-content-center align-items-center">
                                                <button type="button" class="btn btn-link text-primary mb-0"
                                                    data-bs-toggle="modal" data-bs-target="#editRoleModal"
                                                    data-name="{{ $e->name }}" data-id="{{ $e->id }}">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-link text-danger mb-0"
                                                    data-bs-toggle="modal" data-bs-target="#deleteRoleModal"
                                                    data-id="{{ $e->id }}">
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
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel">Tambah Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form id="FromEmployee">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name Employee</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" placeholder="nip"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="position" class="form-label">Position</label>
                            <select name="position" id="position" required>
                                <option value="" selected>Select Position</option>
                            </select>
                        </div>
                        <button type="button" id="btn-submit" onclick="StoreEmployee()"
                            class="btn btn-primary">Simpan</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#position').selectize({
                placeholder: 'Select Position',
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                load: function(query, callback) {
                    if (!query.length) return callback();
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


        async function StoreEmployee() {
            event.preventDefault();

            const form = document.getElementById('FromEmployee');
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
    </script>
@endsection
