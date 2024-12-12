@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Payroll History'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Payroll History</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="container">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="PayrollHistoryTable">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            No
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            No Transaction
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Periode</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Amount</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Total Karyawan</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Status</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Description</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index = 1; @endphp
                                    @foreach ($payrollHistories as $e)
                                        <tr>
                                            <td class="align-middle text-center">{{ $index++ }}</td>
                                            <td>{{ $e->id_transaksi_payment }}</td>
                                            <td>{{ \Carbon\Carbon::parse($e->start_periode)->format('d F') }} -
                                                {{ \Carbon\Carbon::parse($e->end_periode)->format('d F Y') }}</td>
                                            <td>{{ $e->amount_transaksi }}</td>
                                            <td>{{ $e->total_karyawan }}</td>
                                            <td>{{ $e->status_payroll }}</td>
                                            <td>{{ $e->description }}</td>

                                            <td class="align-middle text-end">
                                                <button type="button" class="btn btn-link text-info mb-0"
                                                    onclick="window.location.href='{{ route('historypayrollDetail.index', ['id' => $e->id]) }}'">
                                                    Detail
                                                </button>
                                                <button type="button" class="btn btn-link text-primary mb-0"
                                                    onclick="editEmployee({{ $e->id }})">Edit</button>
                                                <button type="button" class="btn btn-link text-danger mb-0"
                                                    data-bs-toggle="modal" data-bs-target="#deleteRoleModal"
                                                    data-id="{{ $e->id }}">Delete</button>
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



    <!-- Modal Create -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel">Tambah Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="createEmployee"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="EditEmployeeModal" tabindex="-1" aria-labelledby="EditEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="EditEmployeeModalLabel">Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editEmployee"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="DetailPayrollModal" tabindex="-1" aria-labelledby="DetailPayrollModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="DetailPayrollModalLabel">Payroll History Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="CloseDetail"></button>
                </div>
                <div class="modal-body">
                    <div id="detailPayroll"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#PayrollHistoryTable').DataTable({
                responsive: true,
            });
        });

        function CloseDetail() {
            $("#DetailPayrollModal").modal("hide");
        }

        function DetailPayroll(id) {
            console.log('Detail Payroll');
            $.ajax({
                url: "{{ url('/history-payroll/detail') }}/" + id,
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    $("#detailPayroll").html(data);
                    $('#DetailPayrollModal').modal('show');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to open Detail form. Please try again later.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        function createEmployee() {
            $.ajax({
                url: "{{ url('/employee/create') }}",
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    $("#createEmployee").html(data);
                    $('#addEmployeeModal').modal('show');
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

        function editEmployee(id) {
            $.ajax({
                url: "{{ url('/employee/edit') }}/" + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $("#editEmployee").html(response.html);
                    $('#EditEmployeeModal').modal('show');
                    const existingPositionId = response.position_id;
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
                                            text: 'Failed to load Position data. Please try again later.',
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
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to open Edit Employee form. Please try again later.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        async function StoreEmployee() {
            event.preventDefault();

            const form = document.getElementById('FromEmployee');
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
