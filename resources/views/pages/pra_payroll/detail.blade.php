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
                            <table class="table" id="mainTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Id Transaksi</th>
                                        <th>Nama Employee</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index = 1; @endphp
                                    @foreach ($detailPayrolls as $id_employee => $payrolls)
                                        <tr style="cursor: pointer;">
                                            <td>{{ $index++ }}</td>
                                            <td>{{ $payrolls->first()->id_transaksi }}</td>
                                            <td>{{ $payrolls->first()->employee->name ?? 'N/A' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="showComponents({{ $id_employee }})">Lihat Komponen</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                    
                            <!-- Tabel Kedua -->
                            <div class="mt-4">
                                <h5>Detail Komponen</h5>
                                <table class="table" id="componentTable">
                                    <thead>
                                        <tr>
                                            <th>Nama Komponen</th>
                                            <th>Jumlah</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="componentTableBody">
                                        @foreach ($detailPayrolls as $id_employee => $payrolls)
                                            @foreach ($payrolls as $payroll)
                                                <tr class="component-row" data-employee-id="{{ $id_employee }}" style="display: none;">
                                                    <td>{{ $payroll->component->name ?? 'N/A' }}</td>
                                                    <td>Rp. {{ number_format($payroll->amount, 0, ',', '.') }}</td>
                                                    <td class="align-middle text-end">
                                                        <button type="button" class="btn btn-link text-primary mb-0"
                                                            onclick="editDetail({{ $payroll->id }})">Edit</button>
                                                        <button type="button" class="btn btn-link text-danger mb-0"
                                                            data-bs-toggle="modal" data-bs-target="#deleteRoleModal"
                                                            >Delete</button>
                                                    </td>
                                                </tr>
                                            @endforeach
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
 <!-- Modal Edit -->
 <div class="modal fade" id="EditDetailModal" tabindex="-1" aria-labelledby="EditDetailModalLabel"
 aria-hidden="true">
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
      function showComponents(employeeId) {
    // Sembunyikan semua baris komponen
    document.querySelectorAll('.component-row').forEach((row) => {
        row.style.display = 'none';
    });

    // Tampilkan baris komponen yang sesuai dengan employeeId
    const selectedRows = document.querySelectorAll(`.component-row[data-employee-id="${employeeId}"]`);
    selectedRows.forEach((row) => {
        row.style.display = '';
    });
}




        $(document).ready(function() {
            $('#componenTable').DataTable({
                responsive: true,
                // language: {
                //     search: "Cari:",
                //     lengthMenu: "Tampilkan _MENU_ entri",
                //     info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                //     infoEmpty: "Tidak ada entri",
                //     zeroRecords: "Tidak ada entri yang cocok",
                //     paginate: {
                //         first: "Pertama",
                //         last: "Terakhir",
                //         next: "Berikutnya",
                //         previous: "Sebelumnya"
                //     }
                // }
            });
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
                                        const filteredData = data.filter(item => item.category === response.category);
                                        console.log("cek data ", response)
                                        callback(filteredData);
                                    },
                                    error: function() {

                                        callback();
                                    }
                                });
                            },
                            
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

        async function StoreEditDetail() {
            event.preventDefault();

            const form = document.getElementById('FormEditDetailPraPayroll');
            const formData = new FormData(form);
            const submitButton = document.getElementById('btn-submit');

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
                    if (progress >= 90) clearInterval(progressInterval);
                }, 200);
                const response = await fetch('/componen/store', {
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
