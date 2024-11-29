@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Position'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6></h6>
                    <button type="button" class="btn btn-primary" onclick="createPosition()">
                        Tambah Position
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="container">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="positionTable" >
                                <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            No.
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Name</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Code</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Param Position</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index = 1; @endphp
                                    @foreach ($positions as $p)
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
                                                <p class="text-sm font-weight-bold mb-0">{{ $p->paramposition->name }}</p>
                                            </td>
                                            <td class="align-middle text-end">
                                                <div class="d-flex px-3 py-1 justify-content-center align-items-center">
                                                    <button type="button" class="btn btn-link text-primary mb-0"
                                                        onclick="editPosition({{ $p->id }})">
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

    <!--Modal Edit--->
    <div class="modal fade" id="editPositionModal" tabindex="-1" aria-labelledby="editPositionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPositionModalLabel">Edit Position</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editPosition"></div>
                </div>
            </div>
        </div>
    </div>

    <!--Modal Create--->
    <div class="modal fade" id="addPositionModal" tabindex="-1" aria-labelledby="addPositionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPositionModalLabel">Tambah Position</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="createPosition"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#positionTable').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Tidak ada entri",
                    zeroRecords: "Tidak ada entri yang cocok",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        });

        function editPosition(id) {
            $.ajax({
                url: "{{ url('/position/edit') }}/" + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $("#editPosition").html(response.html);
                    $('#editPositionModal').modal('show');
                    const existingPositionId = response.parent_id

                    $('#positionsedit').selectize({
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
                                        text: 'Failed to load position data. Please try again later.',
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

        function createPosition() {
            $.ajax({
                url: "{{ url('/position/create') }}",
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    $("#createPosition").html(data);
                    $('#addPositionModal').modal('show');
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
                                            text: 'Failed to load position data. Please try again later.',
                                            confirmButtonText: 'OK'
                                        });
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
                        text: 'Failed to open create position form. Please try again later.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        async function StorePosition() {
            event.preventDefault();

            const form = document.getElementById('FormPosition');
            const formData = new FormData(form);
            const submitButton = document.getElementById('btn-submit');

            submitButton.disabled = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/position/store', {
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

        async function StoreEditPosition(id) {
            event.preventDefault();

            const form = document.getElementById('FormEditPosition');
            const formData = new FormData(form);
            const submitButton = document.getElementById('btn-submit');

            submitButton.disabled = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/position/storeedit/' + id, {
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
