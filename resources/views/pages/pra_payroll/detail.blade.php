@extends('../layouts.app')


@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Pra Payroll Detail'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="container">
                        <div class="table-responsive">
                            <!-- Tabel Utama -->
                            <h5>Daftar Karyawan</h5>
                            <div class="card">
                                <div class="card-header">
                                    <table class="table" id="praPayrollTable">
                                        <thead>
                                            <tr>
                                                <th></th> <!-- Untuk dt-control -->
                                                <th>No</th>
                                                <th>Nama Employee</th>
                                                <th>NIP</th>
                                                <th>Group</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $index = 1; @endphp
                                            @foreach ($detailPayrolls as $id_employee => $payrolls)
                                                <tr data-employee-id="{{ $id_employee }}"
                                                    data-payrolls="{{ json_encode(
                                                        $payrolls->map(function ($payroll) {
                                                            return [
                                                                'id' => $payroll->id,
                                                                'id_transaksi' => $payroll->id_transaksi,
                                                                'component_name' => $payroll->component->name ?? 'N/A',
                                                                'amount' => $payroll->amount,
                                                            ];
                                                        }),
                                                    ) }}">
                                                    <td class="dt-control"></td>
                                                    <td>{{ $index++ }}</td>
                                                    <td>{{ $payrolls->first()->employee->name ?? 'N/A' }}</td>
                                                    <td>{{ $payrolls->first()->employee->nip }}</td>
                                                    <td>Dummy</td>
                                                    <td>Dummy</td>
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
        </div>
    </div>
    <!-- Modal Edit -->
    <div class="modal fade" id="EditDetailModal" tabindex="-1" aria-labelledby="EditDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="EditDetailModalLabel">Edit Pra Payroll Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editDetail"></div>
                </div>
            </div>
        </div>
    </div>

    </div>

    <script>
        $(document).ready(function() {
            let table = $('#praPayrollTable').DataTable({
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }],
                responsive: true,
                scrollX: true,
                scrollCollapse: true,
                autoWidth: false,
                pageLength: 10,
                pagingType: 'simple_numbers',
                language: {
                    search: "Cari:"
                }
            });

            // Child row click handler
            $('#praPayrollTable tbody').on('click', 'td.dt-control', function() {
                let tr = $(this).closest('tr');
                let row = table.row(tr);
                let employeeId = tr.attr('data-employee-id');
                let payrolls = JSON.parse(tr.attr('data-payrolls'));

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    row.child(format(payrolls)).show();
                    tr.addClass('shown');
                }
            });

            // Format function untuk menampilkan detail
            function format(payrolls) {
                if (!payrolls || payrolls.length === 0) return 'No data available';

                let detail = '<div class="card-body">';
                detail += '<table class="table table-borderless">';
                detail += '<thead><tr>';
                detail += '<th>Id Transaksi</th>';
                detail += '<th>Nama Komponen</th>';
                detail += '<th>Jumlah</th>';
                detail += '<th>Action</th>';
                detail += '</tr></thead>';
                detail += '<tbody>';

                payrolls.forEach(function(payroll) {
                    detail += '<tr>';
                    detail += `<td>${payroll.id_transaksi}</td>`;
                    detail += `<td>${payroll.component_name}</td>`;
                    detail += `<td>Rp. ${new Intl.NumberFormat('id-ID').format(payroll.amount)}</td>`;
                    detail += '<td>';
                    detail +=
                        `<button type="button" class="btn btn-link text-primary mb-0" onclick="editDetail(${payroll.id})">Edit</button>`;
                    detail +=
                        `<button type="button" class="btn btn-link text-danger mb-0" onclick="deleteDetail(${payroll.id})">Delete</button>`;
                    detail += '</td>';
                    detail += '</tr>';
                });

                detail += '</tbody></table>';
                detail += '</div>';
                return detail;
            }
        });

        function editDetail(id) {
            $.ajax({
                url: "{{ url('/pra-payroll/edit/detail') }}/" + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('tes', response.html)
                    $("#editDetail").html(response.html);
                    $('#EditDetailModal').modal('show');
                    $(document).ready(function() {
                        $('#component').selectize({
                            placeholder: response.param_name,
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            preload: true,
                            load: function(query, callback) {
                                $.ajax({
                                    url: '/pra-payroll/list',
                                    type: 'GET',
                                    dataType: 'json',
                                    data: {
                                        q: query
                                    },
                                    success: function(data) {
                                        const filteredData = data
                                            .filter(item => item
                                                .category ===
                                                response.category);
                                        callback(filteredData);
                                    },
                                    error: function() {

                                        callback();
                                    }
                                });
                            },
                            onChange: function(value) {
                                value = response.amount
                                console.log(value)
                                $('#amount').val(value);

                            }
                        });
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to open Edit Componen form. Please try again later.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        async function StoreEditDetail(id) {
            event.preventDefault();

            const form = document.getElementById('FormEditDetailPraPayroll');
            const formData = new FormData(form);
            const submitButton = document.getElementById('btn-submit');

            submitButton.disabled = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/pra-payroll/update/detail/' + id, {
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
        async function deleteDetail(id) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Menampilkan konfirmasi sebelum menghapus data
            const result = await Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true, // Menampilkan tombol batal
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true // Menempatkan tombol Batal di sebelah kiri
            });

            // Jika pengguna mengklik "Ya, hapus!", lanjutkan proses penghapusan
            if (result.isConfirmed) {
                try {
                    const response = await fetch('/pra-payroll/delete/detail/' + id, {
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

                    const data = await response.json();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Absen berhasil dihapus.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Tindakan setelah sukses (misalnya reload atau perbarui tampilan)
                        location.reload(); // Bisa diganti sesuai kebutuhan
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
    </script>
@endsection
