@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Run Payroll'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Run Payroll</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="container">
                        <div class="container-setting">

                            <form id="runpayroll">
                                @csrf
                                <div class="form-group">
                                    <label for="start_date" class="col-form-label">Start Date</label>
                                    <input type="date" class="col-sm-2 form-control" id="start_date" name="start_date"
                                        readonly>
                                </div>

                                <div class="form-group">
                                    <label for="end_date" class="col-form-label">End Date</label>
                                    <input type="date" class="col-sm-2 form-control" id="end_date" name="end_date"
                                        readonly>
                                </div>

                                <button type="button" class="btn btn-outline-primary"
                                    onclick="setDatesForPreviousWeeks()">Set Payroll Dates (2 Weeks Back)</button>

                                <div class="form-group">
                                    <label for="floatingTextarea" class="col-form-label">Description</label>
                                    <textarea class="col-sm-5 form-control" placeholder="Description..." id="floatingTextarea" name="description"></textarea>
                                </div>
                                {{-- <button type="button" class="btn btn-outline-primary" onclick="showEmploySelected()">Add
                                    Employee</button>
                                <div id="selected-employees" class="mt-3">
                                    <h5>Selected Employees:</h5>
                                    <ul id="employee-list"></ul>
                                </div> --}}
                                <div class="d-flex justify-content-center"><button type="button"
                                        class="btn btn-outline-primary" onclick="runpayroll()">RunPayroll</button></div>


                            </form>



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function Close() {
            $("#EmployeeModal").modal("hide");
        }

        function showEmploySelected() {
            $.get("{{ url('/run-payroll/employee') }}", function(data, status) {

                $("#pageemployrun").html(data);
                $('#EmployeeModal').modal('show');

                loadEmployeeDataRun();
            });
        }

        function loadEmployeeDataRun() {
            $.ajax({
                url: '{{ url('/employee/employee-list') }}',
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $('#items').empty();
                    totalEmployees = res.length;
                    res.forEach(function(employee) {
                        $('#items').append('<option value="' +
                            employee.id + '" data-nomor-induk-karyawan="' +
                            employee.nomor_induk_karyawan + '">' +
                            employee.nama_lengkap + ' - ' +
                            employee.nomor_induk_karyawan + ' (' +
                            employee.jabatan_nama + ')</option>');
                    });
                    updateViewEmployeeCount();
                },
                error: function(err) {
                    console.log("Error loading employees data: ", err);
                }
            });
        }

        let selectedEmployees = [];

        function showEmployRun() {
            fetch('/run-payroll/get-selected-employees')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        selectedEmployees = data.data;
                        const employeeList = document.getElementById('employee-list');
                        employeeList.innerHTML = '';

                        data.data.forEach(employee => {
                            const listItem = document.createElement('li');
                            listItem.textContent = `${employee.name} (NIK: ${employee.nik})`;
                            employeeList.appendChild(listItem);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Data gagal disimpan'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Data gagal disimpan: ' + error
                    });
                });
        }

        function runpayroll() {
            event.preventDefault();

            const employeeIds = selectedEmployees.map(employee => employee.id);
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            if (!startDate || !endDate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select both start and end dates.',
                });
                return false;
            }
            const description = $('#floatingTextarea').val();
            const payload = {
                start_date: startDate,
                end_date: endDate,
                description: description,
            };
            //console.log('Payload:', payload);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
            });

            $.ajax({
                url: "/run-payroll/store",
                type: "POST",
                data: JSON.stringify(payload),
                contentType: "application/json",
                processData: false,
                cache: false,
                success: function(data) {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Payroll processed successfully!',
                            confirmButtonText: 'OK',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to process payroll.',
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    let errorMessage = 'Error occurred while processing your request. Please try again later.';
                    if (xhr.status === 500 && xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }

                    if (xhr.status === 422) {
                        const response = xhr.responseJSON;

                        if (response.message) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: response.message,
                            });
                        } else if (response.errors) {
                            let errorList = '';
                            $.each(response.errors, function(key, value) {
                                errorList += `<li>${value[0]}</li>`;
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Errors',
                                html: `<ul>${errorList}</ul>`,
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                        });
                    }
                }
            });
        }

        function getPreviousWeeksDates() {
            const today = new Date(); // Current date
            const dayOfWeek = today.getDay(); // Day of the week (0 = Sunday, 6 = Saturday)
            const daysSinceMonday = (dayOfWeek + 6) % 7; // Days since last Monday

            // Calculate the start (Monday) of the previous week
            const start = new Date(today);
            start.setDate(today.getDate() - daysSinceMonday - 7);

            // Calculate the end (Sunday) of the previous week
            const end = new Date(start);
            end.setDate(start.getDate() + 6);

            // Format dates as YYYY-MM-DD
            return {
                start: formatDate(start),
                end: formatDate(end),
            };
        }

        function formatDate(date) {
            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        }

        function setDatesForPreviousWeeks() {
            const weekDates = getPreviousWeeksDates();

            document.getElementById("start_date").value = weekDates.start;
            document.getElementById("end_date").value = weekDates.end;
        }

        setDatesForPreviousWeeks();
    </script>

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <div class="modal fade modselect" id="EmployeeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Select Employee</h5>
                    <button type="button" class="close" onClick="Close()" id="close-button" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="pageemployrun" class="p-2"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
