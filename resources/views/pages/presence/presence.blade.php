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
                        data-bs-target="#addPresenceModal" onclick="createPresence()">
                        Tambah Presence
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="presenceTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Employee Name</th>
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
                                        <td>{{ $presence->employee->name }}</td>
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

     <!-- Modal Create -->
     <div class="modal fade" id="addPresenceModal" tabindex="-1" aria-labelledby="addPresenceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPresenceModalLabel">Tambah Presence</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="createPresence"></div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Create End--}}

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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeModalImportPresence()">Tutup</button>
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
                    console.log("cek UHUYY : ", response);

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
            // Membuat objek untuk menyimpan hanya satu entri per NIP
            const uniqueData = {};

            // Memasukkan data ke dalam objek berdasarkan NIP
            data.forEach(item => {
                if (!uniqueData[item.nip]) {
                    uniqueData[item.nip] = item; // Simpan entri pertama untuk setiap NIP
                }
            });
            // Ambil nilai objek uniqueData dan ubah menjadi array untuk pemrosesan lebih lanjut
            const filteredData = Object.values(uniqueData);
            let tableHtml = `
<table class="table align-items-center mb-0">
    <thead>
        <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">NIP</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status Karyawan</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Validasi Data</th>
            </tr>
            </thead>
            <tbody>
                `;

            filteredData.forEach(item => {
                console.log("ada apa ini", item)
                tableHtml += `
                 <tr class="clickable-row" data-nip="${item.nip}">
                    <td>${item.nip || '-'}</td>
                    <td>${item.nama || '-'}</td>
                    <td>${item.status_karyawan[0] || '-'}</td>
                    <td>${item.validasi_data[0] || '-'}</td>
                </tr>
            `;
            });

            tableHtml += `
                </tbody>
            </table>
        `;

            // Tambahkan ke div tabel di dalam preview
            $('#table-preview').html(tableHtml);

            // Menambahkan event listener untuk baris yang dapat diklik
            document.querySelectorAll('.clickable-row').forEach(row => {
                row.addEventListener('click', function() {
                    const nip = this.getAttribute('data-nip');
                    showFullDetails(nip); // Menampilkan data lengkap berdasarkan NIP
                });
            });

            // Fungsi untuk menampilkan detail data berdasarkan NIP
            function showFullDetails(nip) {
                const selectedItems = data.filter(item => item.nip === nip);
                if (selectedItems.length > 0) {
                    // Membuat konten modal dengan mengelompokkan data berdasarkan NIP
                    let modalContent = `<h4>Data untuk NIP: ${nip}</h4>`;


                    modalContent += `
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jam Masuk</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jam Pulang</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Validasi Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                
            `;
                    selectedItems.forEach(item => {
                        modalContent += `
                 <tr class="clickable-row" data-nip="${item.nip}">
                    <td>${item.tanggal || '-'}</td>
                    <td>${item.jam_masuk || '-'}</td>
                    <td>${item.jam_pulang || '-'}</td>
                    <td>${item.presensi_status || '-'}</td>
                    <td>${item.validasi_error || '-'}</td>
                </tr>
            `;
                    });

                    modalContent += `
                </tbody>
            </table>
        `;
                    document.getElementById('content-absen').innerHTML = modalContent;
                    document.getElementById('detailModal').style.display = 'block';
                }
            }


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

            console.log('cekkkkkk:', dataToSave);

            // Atur header CSRF
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            // Kirim data ke server menggunakan AJAX
            $.ajax({
                url: "{{ route('presence.storeImport') }}",
                method: "POST",
                data: {
                    data: dataToSave
                },
                success: function(response) {
                    console.log(response);
                    clearInterval(progressInterval); // Hentikan progres
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil disimpan'
                    }).then(() => {
                        location.reload(); // Refresh halaman setelah berhasil
                    });
                    $('#saveButton').hide();
                    submitButton.disabled = false; // Sembunyikan tombol setelah berhasil disimpan
                },
                error: function(xhr, status, error) {
                    clearInterval(progressInterval);

                    let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).flat().join(', ');
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: errorMessage
                    });

                    console.error(xhr.responseText); // Log detail error di konsol
                    submitButton.disabled = false; // Aktifkan kembali tombol untuk mencoba lagi
                },
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

        function closeModal() {
            document.getElementById('detailModal').style.display = 'none';
        }
        function closeModalImportPresence() {
            document.getElementById('importModal').style.display = 'none';
        }

        function editPresence(id) {
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
