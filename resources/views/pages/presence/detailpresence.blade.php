@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Presence'])
    @include('sweetalert::alert')

    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    @foreach ($uniqueEmployees as $uniqName)
                    <h6>Detail Presence : {{ $uniqName->name }}  </h6>
                    @endforeach
                    <div class="d-flex">
                        <input type="date" id="startDate" class="form-control form-control-sm me-2" placeholder="Start Date">
                        <input type="date" id="endDate" class="form-control form-control-sm me-2" placeholder="End Date">
                        <button id="filterButton" class="btn btn-primary btn-sm" onclick="filterPresences()">Filter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="presenceTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Scan</th>
                                    <th>Tanggal</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>SN</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $index = 1; @endphp
                                @foreach ($presences as $presence)
                                    <tr>
                                        <td>{{ $index++ }}</td>
                                        <td>{{ $presence->tanggal_scan }}</td>
                                        <td>{{ $presence->tanggal }}</td>
                                        <td>{{ $presence->jam_masuk }}</td>
                                        <td>{{ $presence->jam_pulang }}</td>
                                        <td>{{ $presence->sn }}</td>
                                        <td>{{ $presence->presensi_status }}</td>
                                        <td class="align-middle text-end">
                                           
                                            <button type="button" class="btn btn-link text-primary mb-0"
                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                onclick="editPresence({{ $presence->id }})">Edit</button>
                                            <button type="button" class="btn btn-link text-danger mb-0"
                                                data-bs-toggle="modal" data-bs-target="#deletePresenceModal" onclick="deletePresence({{ $presence->id }})"
                                                data-id="{{ $presence->id }}">Delete</button>
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

    {{-- Modal Edit --}}
    <div class="modal fade" id="EditPresenceModal" tabindex="-1" aria-labelledby="addParamPositionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addParamPositionModalLabel">Edit Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editPresence"></div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Edit End --}}
    <div id="detailModal" class="modal">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Select Component</h5>
                    <button type="button" class="close" onclick="closeModal()" id="close-button" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="content-absen" class="p-2"></div>
                </div>
            </div>
        </div>
    </div>

  
    <script>
        function filterPresences() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    if (!startDate || !endDate) {
        alert('Please select both start and end dates!');
        return;
    }

    // Kirim request dengan AJAX
    fetch(`/presence/filter-presences?start=${startDate}&end=${endDate}`)
        .then(response => response.json())
        .then(data => {
            // Update tabel berdasarkan hasil filter
            const tableBody = document.querySelector('#presenceTable tbody');
            tableBody.innerHTML = '';

            if (data.length > 0) {
                data.forEach((presence, index) => {
                    const formattedTanggal = new Date(presence.tanggal).toLocaleDateString('en-CA');
                    const jamMasuk = presence.jam_masuk ? presence.jam_masuk : '';
                    const jamPulang = presence.jam_pulang ? presence.jam_pulang : '';
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${presence.tanggal_scan}</td>
                            <td>${formattedTanggal}</td>
                            <td>${jamMasuk}</td>
                            <td>${jamPulang}</td>
                            <td>${presence.sn}</td>
                            <td>${presence.presensi_status}</td>
                            <td class="align-middle text-end">
                                <button type="button" class="btn btn-link text-primary mb-0"
                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                    onclick="editPresence(${presence.id})">Edit</button>
                                <button type="button" class="btn btn-link text-danger mb-0"
                                    data-bs-toggle="modal" data-bs-target="#deletePresenceModal"
                                    onclick="deletePresence(${presence.id})">Delete</button>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center">No data found</td></tr>';
            }
        })
        .catch(error => console.error('Error:', error));
}


        function editPresence(id) {
            // console.log('cek id : ', id)
            $.ajax({
                url: "{{ url('/presence/edit') }}/" + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $("#editPresence").html(response.html);
                    $('#EditPresenceModal').modal('show');
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
        async function StoreEditPresence(id) {
            event.preventDefault();

            const form = document.getElementById('FormEditPresence');
            const formData = new FormData(form);
            const submitButton = document.getElementById('btn-submit');

            submitButton.disabled = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/presence/update/' + id, {
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
        async function deletePresence(id) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Menampilkan konfirmasi sebelum menghapus data
    const result = await Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Data ini akan dihapus secara permanen!',
        icon: 'warning',
        showCancelButton: true,  // Menampilkan tombol batal
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true  // Menempatkan tombol Batal di sebelah kiri
    });

    // Jika pengguna mengklik "Ya, hapus!", lanjutkan proses penghapusan
    if (result.isConfirmed) {
        try {
            const response = await fetch('/presence/delete/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                const errorData = await response.text(); // Ambil teks respons
                throw new Error(errorData || 'Gagal menghapus data.');
            }
            console.log("tess", response)
            const data = await response.json();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Absen berhasil dihapus.',
                confirmButtonText: 'OK'
            }).then(() => {
                // Tindakan setelah sukses (misalnya reload atau perbarui tampilan)
                location.reload();  // Bisa diganti sesuai kebutuhan
            });

        } catch (error) {
            console.error('Error:', error);

            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: error.message || 'Terjadi kesalahan saat menghapus absen.',
                confirmButtonText: 'Coba Lagi'
            });
        }
    }
}
function createPresence() {
            $.ajax({
                url: "{{ url('/presence/create') }}",
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    $("#createPresence").html(data);
                    $('#addPresenceModal').modal('show');
                    $(document).ready(function() {
                        $('#employeeName').selectize({
                            placeholder: 'Select Employee Name',
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            preload: true,
                            load: function(query, callback) {
                                $.ajax({
                                    url: '/presence/list',
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

async function StorePresence() {
            event.preventDefault();

            const form = document.getElementById('FormPresence');
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
                const response = await fetch('/presence/store', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });
                clearInterval(progressInterval);

                if (!response.ok) {
                    const errorData = await response.json();
                    console.log("cek", errorData)
                    throw new Error(errorData.error|| 'Gagal menyimpan data.');
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
