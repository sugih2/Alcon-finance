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

                            <form id=runpayroll>
                                @csrf
                                <div class="form-group">
                                    <label for="periode" class="col-form-label">Periode Start</label>
                                    <input type="date" class="col-sm-2 form-control" id="periode" placeholder="Periode"
                                        name="start_date">
                                </div>

                                <div class="form-group">
                                    <label for="periode" class="col-form-label">Periode End</label>
                                    <input type="date" class="col-sm-2 form-control" id="periode_end"
                                        placeholder="Periode" name="end_date">
                                </div>

                                <div class="form-group">
                                    <label for="floatingTextarea" class="col-form-label">Description</label>
                                    <textarea class="col-sm-5 form-control" placeholder="Optional" id="floatingTextarea" name="description"></textarea>
                                </div>
                                <button type="button" class="btn btn-outline-primary" onclick="showEmploySelected()">Add
                                    Employee</button>
                                <div id="selected-employees" class="mt-3">
                                    <h5>Selected Employees:</h5>
                                    <ul id="employee-list"></ul>
                                </div>
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

            const employeeIds = selectedEmployees.map(employee => employee.id);

            const payload = {
                efektif_date: document.getElementById('periode').value,
                end_date: document.getElementById('periode_end').value,
                description: document.getElementById('floatingTextarea').value,
                employee_ids: employeeIds,
            };

            console.log(payload);

            fetch('/run-payroll/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify(payload),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Payroll processed successfully!');
                    } else {
                        alert('Failed to process payroll');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
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
