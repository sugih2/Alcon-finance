@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Presence'])
    @include('sweetalert::alert')

    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Preview</h6>
                    <button id="saveButton" class="btn btn-success btn-sm" style="display:none;">Save</button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0" id="table-preview">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Presence List</h6>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                        Import Presence
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addPresenceModal">
                        Tambah Presence
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="presenceTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Employee ID</th>
                                    <th>Tanggal Scan</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>SN</th>
                                    <th>Action</th>
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
                                            <button type="button" class="btn btn-link text-primary mb-0"
                                                data-bs-toggle="modal" data-bs-target="#editPresenceModal"
                                                data-id="{{ $presence->id }}">Edit</button>
                                            <button type="button" class="btn btn-link text-danger mb-0"
                                                data-bs-toggle="modal" data-bs-target="#deletePresenceModal"
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



    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="importForm" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Presence</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="file">Pilih File (XML/XLS)</label>
                            <input type="file" class="form-control" name="file" id="file"
                                accept=".xml, .xls, .xlsx" required>
                        </div>
                        <div class="form-group">
                            <label for="start_date">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date" id="start_date" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date">Tanggal Akhir</label>
                            <input type="date" class="form-control" name="end_date" id="end_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="submitImportForm()">Proses Data</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function submitImportForm() {
            $('#importForm').submit();
        }

        $('#importForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('presence.processImport') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Debug log untuk memeriksa data yang diterima
                    console.log(response);

                    if (response.data && response.data.length > 0) {
                        generateTable(response.data);
                        $("#importModal").modal("hide");
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Data tidak ditemukan'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan saat memproses data');
                }
            });
        });

        function generateTable(data) {
            let tableHtml = `
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">NIP</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jam Masuk</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jam Pulang</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Presensi Status</th>
                    </tr>
                </thead>
                <tbody>
        `;

            data.forEach(item => {
                tableHtml += `
                <tr>
                    <td>${item.nip || '-'}</td>
                    <td>${item.nama || '-'}</td>
                    <td>${item.tanggal || '-'}</td>
                    <td>${item.jam_masuk || '-'}</td>
                    <td>${item.jam_pulang || '-'}</td>
                    <td>${item.presensi_status || '-'}</td>
                </tr>
            `;
            });

            tableHtml += `
                </tbody>
            </table>
        `;

            // Tambahkan ke div tabel di dalam preview
            $('#table-preview').html(tableHtml);

            if (data.length > 0) {
                $('#saveButton').show().data('data', data); // Simpan data di tombol Save
            } else {
                $('#saveButton').hide();
            }
        }

        // Fungsi untuk menyimpan data setelah preview
        $('#saveButton').on('click', function() {
            let dataToSave = $(this).data('data'); // Ambil data yang telah disimpan di tombol
            const submitButton = this;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Nonaktifkan tombol untuk mencegah klik ganda
            submitButton.disabled = true;

            Swal.fire({
                title: 'Menyimpan data...',
                html: 'Progress: <b>0%</b>',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += 10;
                Swal.update({
                    html: `Progress: <b>${progress}%</b>`
                });
                if (progress >= 90) clearInterval(progressInterval); // Stop updating near completion
            }, 200);

            console.log('cekkkkkk:'.dataToSave);

            // Atur header CSRF
            // $.ajaxSetup({
            //     headers: {
            //         'X-CSRF-TOKEN': csrfToken
            //     }
            // });

            // // Kirim data ke server menggunakan AJAX
            // $.ajax({
            //     url: "{{ route('presence.storeImport') }}",
            //     method: "POST",
            //     data: {
            //         data: dataToSave
            //     },
            //     success: function(response) {
            //         clearInterval(progressInterval); // Hentikan progres
            //         Swal.fire({
            //             icon: 'success',
            //             title: 'Berhasil',
            //             text: 'Data berhasil disimpan'
            //         }).then(() => {
            //             location.reload(); // Refresh halaman setelah berhasil
            //         });
            //         $('#saveButton').hide();
            //         submitButton.disabled = false; // Sembunyikan tombol setelah berhasil disimpan
            //     },
            //     error: function(xhr, status, error) {
            //         clearInterval(progressInterval); // Hentikan progres
            //         Swal.fire({
            //             icon: 'error',
            //             title: 'Gagal',
            //             text: 'Terjadi kesalahan saat menyimpan data.'
            //         });
            //         console.error(xhr.responseText); // Log detail error
            //         submitButton.disabled = false; // Aktifkan kembali tombol untuk mencoba lagi
            //     }
            // });
        });


        // Initialize DataTable
        $(document).ready(function() {
            $('#presenceTable').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                    zeroRecords: "Tidak ditemukan data",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        });
    </script>
@endsection
