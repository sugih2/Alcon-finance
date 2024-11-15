@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Presence'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Presence List</h6>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPresenceModal">
                        Tambah Presence
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="presenceTable">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Employee ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal Scan</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Jam</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">SN</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $index = 1; @endphp
                                @foreach ($presences as $presence)
                                    <tr>
                                        <td>{{ $index++ }}</td>
                                        <td>{{ $presence->employed_id }}</td>
                                        <td>{{ $presence->tanggal_scan }}</td>
                                        <td>{{ $presence->tanggal }}</td>
                                        <td>{{ $presence->jam }}</td>
                                        <td>{{ $presence->sn }}</td>
                                        <td class="align-middle text-end">
                                            <button type="button" class="btn btn-link text-primary mb-0" data-bs-toggle="modal" data-bs-target="#editPresenceModal" data-id="{{ $presence->id }}">Edit</button>
                                            <button type="button" class="btn btn-link text-danger mb-0" data-bs-toggle="modal" data-bs-target="#deletePresenceModal" data-id="{{ $presence->id }}">Delete</button>
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

    <!-- Modal Create Presence -->
    <div class="modal fade" id="addPresenceModal" tabindex="-1" aria-labelledby="addPresenceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPresenceModalLabel">Tambah Presence</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="FormPresence">
                        @csrf
                        <div class="mb-3">
                            <label for="employed_id" class="form-label">Employee ID</label>
                            <input type="text" class="form-control" id="employed_id" name="employed_id" placeholder="Employee ID" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_scan" class="form-label">Tanggal Scan</label>
                            <input type="date" class="form-control" id="tanggal_scan" name="tanggal_scan" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>
                        <div class="mb-3">
                            <label for="jam" class="form-label">Jam</label>
                            <input type="time" class="form-control" id="jam" name="jam" required>
                        </div>
                        <div class="mb-3">
                            <label for="sn" class="form-label">SN</label>
                            <input type="text" class="form-control" id="sn" name="sn" placeholder="SN" required>
                        </div>
                        <button type="button" id="btn-submit" onclick="storePresence()" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#presenceTable').DataTable({
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

        async function storePresence() {
            event.preventDefault();

            const form = document.getElementById('FormPresence');
            const formData = new FormData(form);
            const submitButton = document.getElementById('btn-submit');

            submitButton.disabled = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/presence/store', {
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
