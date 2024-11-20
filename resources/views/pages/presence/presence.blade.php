@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Presence'])
    @include('sweetalert::alert')
    
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Presence List</h6>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                        Import Presence
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPresenceModal">
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
                            <input type="file" class="form-control" name="file" id="file" accept=".xml, .xls, .xlsx" required>
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
    
    <!-- Modal Proses Data -->
    <div class="modal fade" id="processDataModal" tabindex="-1" role="dialog" aria-labelledby="processDataModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="processDataModalLabel">Proses Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Proses data sedang berlangsung...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Preview Data -->
    <div class="modal fade" id="previewDataModal" tabindex="-1" role="dialog" aria-labelledby="previewDataModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewDataModalLabel">Preview Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h3>Preview Data</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="previewTable"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" id="saveData" class="btn btn-success">Simpan Data</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>   

    <script>
        function submitImportForm() {
            $('#importForm').submit();
        }

        // Handle form submission using AJAX
        $('#importForm').on('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('presence.processImport') }}",  // Endpoint untuk memproses import
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    // Debug log untuk memeriksa data yang diterima
                    console.log(response);
                    
                    if (response.data && response.data.length > 0) {
                        // Menampilkan modal preview
                        $('#previewDataModal').modal('show');
                        
                        let tableContent = '';
                        response.data.forEach((row, index) => {
                            tableContent += `<tr>
                                <td>${index + 1}</td>
                                <td>${row.tanggal}</td>
                                <td>${row.jam_masuk}</td>
                                <td>${row.jam_keluar}</td>
                                <td>Valid</td>
                            </tr>`;
                        });
                        
                        response.invalidData.forEach((row, index) => {
                            tableContent += `<tr class="text-danger">
                                <td>${index + 1}</td>
                                <td>${row.tanggal}</td>
                                <td>${row.jam}</td>
                                <td>-</td>
                                <td>Invalid</td>
                            </tr>`;
                        });
                        
                        $('#previewTable').html(tableContent);  // Menampilkan data ke tabel preview
                    } else {
                        alert('Tidak ada data untuk dipreview');
                    }
                },
                error: function (xhr, status, error) {
                    alert('Terjadi kesalahan saat memproses data');
                }
            });
        });

        // Fungsi untuk menyimpan data setelah preview
        $('#saveData').on('click', function () {
            const validatedData = [];  // Ambil data valid yang telah divalidasi

            // Submit data menggunakan AJAX
            $.ajax({
                url: "{{ route('presence.storeImport') }}",  // Endpoint untuk menyimpan data
                method: "POST",
                data: { data: validatedData },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Data berhasil disimpan'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Data gagal disimpan'
                    });
                }
            });
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
