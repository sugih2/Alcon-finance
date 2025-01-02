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
                                            <td>Rp {{ number_format($e->amount_transaksi, 0, ',', '.') }}</td>
                                            <td>{{ $e->total_karyawan }}</td>
                                            <td>{{ $e->status_payroll }}</td>
                                            <td>{{ $e->description }}</td>

                                            <td class="align-middle text-end">
                                                <button type="button" class="btn btn-link text-info mb-0"
                                                    onclick="window.location.href='{{ route('historypayrollGroup.index', ['id' => $e->id]) }}'">
                                                    Detail Group
                                                </button>
                                                <button class="btn btn-sm"
                                                    onclick="locking({{ $e->id }}, {{ $e->locking }}, '{{ $e->start_periode }}', '{{ $e->end_periode }}')">
                                                    <i>
                                                        {!! $e->locking
                                                            ? '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16">
                                                                                                                                                                                                                                                                                                                                                                   <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/>
                                                                                                                                                                                                                                                                                                                                                               </svg>'
                                                            : '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-unlock-fill" viewBox="0 0 16 16">
                                                                                                                                                                                                                                                                                                                                                                   <path d="M11 1a2 2 0 0 0-2 2v4a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5V3a3 3 0 0 1 6 0v4a.5.5 0 0 1-1 0V3a2 2 0 0 0-2-2"/>
                                                                                                                                                                                                                                                                                                                                                               </svg>' !!}
                                                    </i>
                                                </button>
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

        // function DetailPayroll(id) {
        //     console.log('Detail Payroll');
        //     $.ajax({
        //         url: "{{ url('/history-payroll/detail') }}/" + id,
        //         type: 'GET',
        //         dataType: 'html',
        //         success: function(data) {
        //             $("#detailPayroll").html(data);
        //             $('#DetailPayrollModal').modal('show');
        //         },
        //         error: function() {
        //             Swal.fire({
        //                 icon: 'error',
        //                 title: 'Error',
        //                 text: 'Failed to open Detail form. Please try again later.',
        //                 confirmButtonText: 'OK'
        //             });
        //         }
        //     });
        // }

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

        function locking(id, locking, startPeriode, endPeriode) {
            let newLocking = locking === 1 ? 0 : 1;
            const Request = {
                id: id,
                locking: newLocking,
                startPeriode: startPeriode,
                endPeriode: endPeriode
            };

            const data = JSON.stringify(Request);

            Swal.fire({
                title: newLocking ? "Confirm Lock" : "Confirm Unlock",
                text: `Are you sure you want to ${newLocking ? 'lock' : 'unlock'} the data for the period ${startPeriode} - ${endPeriode}?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: newLocking ? "Yes, Lock it!" : "Yes, Unlock it!",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Content-Type': 'application/json',
                        }
                    });
                    $.ajax({
                        url: "{{ route('historypayroll.locking') }}",
                        method: "POST",
                        data: data,
                        success: function(response) {
                            if (response.success) {
                                let button = $(`button[onclick*="${id}"]`);
                                button.find('i').toggleClass('bi-lock bi-unlock');
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                    timer: 2000,
                                });
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.error || {};
                                var errorMessage = "Reason:\n";

                                if (typeof errors === "object") {
                                    for (var key in errors) {
                                        if (errors.hasOwnProperty(key)) {
                                            errorMessage += `- ${errors[key]}\n`;
                                        }
                                    }
                                } else {
                                    errorMessage += `- ${errors}\n`;
                                }

                                Swal.fire({
                                    icon: "error",
                                    title: "Validation Error",
                                    text: errorMessage,
                                    customClass: {
                                        content: 'text-left'
                                    }
                                });
                            } else if (xhr.status === 500) {
                                var serverErrorMessage = xhr.responseJSON.error ||
                                    "An internal server error occurred.";
                                Swal.fire({
                                    icon: "error",
                                    title: "Server Error",
                                    text: serverErrorMessage,
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Unknown Error",
                                    text: "An error occurred on the server. Please try again later.",
                                });
                            }
                        }
                    });
                }
            });
        }

        $(document).on('click', '.toggle-lock', function() {
            locking($(this));
        });
    </script>
@endsection
