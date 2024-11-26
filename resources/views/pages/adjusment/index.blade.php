@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Adjusment'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Setting Information</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="container">
                        <div class="container-main">
                            <div class="container-setting">

                                <form>

                                    <div class="form-group">
                                        <label for="gridRadios1" class="col-form-label">Type</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type" id="gridRadios1"
                                                value="adjustment">
                                            <label class="form-check-label" for="gridRadios1">Adjustment</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type" id="gridRadios2"
                                                value="expired">
                                            <label class="form-check-label" for="gridRadios2">Expired</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="periode" class="col-form-label">Efektif Date</label>
                                        <input type="date" class="col-sm-2 form-control" id="periode"
                                            placeholder="Periode" name="efektif_date">
                                    </div>

                                    <div class="form-group">
                                        <label for="periode" class="col-form-label">End Date</label>
                                        <input type="date" class="col-sm-2 form-control" id="periode_end"
                                            placeholder="Periode" name="end_date">
                                    </div>

                                    <div class="form-group">
                                        <label for="floatingTextarea" class="col-form-label">Description</label>
                                        <textarea class="col-sm-5 form-control" placeholder="Optional" id="floatingTextarea" name="description"></textarea>
                                    </div>
                                </form>

                            </div>

                            <div class="container-manage">
                                <div class="d-flex">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        fill="currentColor" class="bi bi-person-badge-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm4.5 0a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6m5 2.755C12.146 12.825 10.623 12 8 12s-4.146.826-5 1.755V14a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1z" />
                                    </svg>
                                    <h5 class="ml-2">Manage Employee</h5>
                                </div>
                                <div class="head-manage">
                                    <button type="button" class="btn btn-outline-primary" onclick="showEmploy()">Add
                                        Employee</button>
                                    <button type="button" id="addComponentButton" class="btn btn-outline-primary"
                                        onclick="showComponent()" disabled>Add
                                        Component</button>
                                    <div class="form-group has-search" id="searchview" style="display: none; float:right;">
                                        <form class="search pencarian" id="searchForm" style="display: flex;">
                                            <span class="fa fa-search form-control-feedback"></span>
                                            <input type="text" class="form-control" placeholder="Search" id="search"
                                                name="search">
                                        </form>
                                    </div>

                                </div>

                                <div class="border-body" id="store" style="display: none">
                                    <div class="table-responsive ">
                                        <table class="table data-table text-uppercase" id="myTable">
                                            <thead class="table-head">
                                                <tr class="judul">
                                                    <th class="name" scope="col-2">Employe Name</th>
                                                    <th class="id" scope="col-2">Employe ID</th>
                                                    <th class="position" scope="col-2">Position</th>
                                                    <th class="komponen-name" scope="col-2" onclick="sortTable(1)">
                                                        Component name
                                                    </th>
                                                    <th class="komponen-type" scope="col-2" onclick="sortTable(2)">
                                                        Component type
                                                    </th>
                                                    <th class="last-amount" scope="col-2" onclick="sortTable(3)">Current
                                                        amount</th>
                                                    <th class="new-amount" scope="col-2" onclick="sortTable(4)">New
                                                        amount</th>
                                                    <th class="date" scope="col-2" onclick="sortTable(5)"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="isi">
                                                    <td class="name" id="nama_lengkap"></td>
                                                    <td class="nik" id="nomor_induk_karyawan"></td>
                                                    <td class="position" id="position"></td>
                                                    <td class="komponen-name" id="nama_component"></td>
                                                    <td class="komponen-type" id="komponen"></td>
                                                    <td class="last-amount" id="amount"></td>
                                                    <td class="new-amount" id="new-amount">
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text" id="basic-addon1"></span>
                                                            <input type="text" class="form-control"
                                                                placeholder="New amount" aria-label="New amount"
                                                                aria-describedby="basic-addon1">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor" class="bi bi-x-circle"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                            <path
                                                                d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                                        </svg>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="paginationControls" class="pagination d-flex justify-content-end"></div>
                                    <div class="btn-storeupdate">
                                        <button type="button" onClick="storeParamPayroll()"
                                            class="btn btn-primary justify-content-end">Submit</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--MODAL-->
    <script>
        function Close() {
            $("#exampleModal").modal("hide");
            $("#exampleModal1").modal("hide");
        }

        function showEmploy() {
            $.get("{{ url('/adjusment/employee') }}", function(data, status) {

                $("#pageemploy").html(data);
                $('#exampleModal').modal('show');

                loadEmployeeData();


                // $(document).ready(function() {
                //     $('#loadEmployees').on('click', function() {
                //         var filterData = $('#filterCriteria').serialize();

                //         $.ajax({
                //             url: '{{ url('/param_payroll/employee/list') }}',
                //             type: 'GET',
                //             data: filterData,
                //             dataType: 'json',
                //             success: function(res) {

                //                 $('#items').empty();
                //                 totalEmployees = res.length;
                //                 res.forEach(function(employee) {
                //                     $('#items').append('<option value="' +
                //                         employee.id +
                //                         '" data-nomor-induk-karyawan="' +
                //                         employee
                //                         .nomor_induk_karyawan + '">' +
                //                         employee.nama_lengkap +
                //                         ' - ' + employee
                //                         .nomor_induk_karyawan + ' (' +
                //                         employee
                //                         .jabatan_nama + ')</option>');
                //                 });
                //                 updateViewEmployeeCount();
                //             },
                //             error: function(err) {
                //                 console.log("Error filtering employees: ", err);
                //             }
                //         });
                //     });
                // });

                // // Inisialisasi Selectize untuk 'cabang'
                // $('#region').selectize({
                //     plugins: ['remove_button'],
                //     persist: false,
                //     create: false,
                //     valueField: 'id',
                //     labelField: 'name',
                //     searchField: 'name',
                //     preload: true,
                //     onInitialize: function() {
                //         var selectize = this;
                //         $.ajax({
                //             url: '{{ url('/param_payroll/region/list') }}',
                //             type: 'GET',
                //             dataType: 'json',
                //             success: function(res) {

                //                 selectize.addOption(res);
                //                 selectize.refreshOptions(
                //                     false); // Refresh options to show data immediately
                //             },
                //             error: function(err) {
                //                 console.log("Error loading data for 'cabang': ", err);
                //             }
                //         });
                //     }
                // });

                // $('#cabang').selectize({
                //     plugins: ['remove_button'],
                //     persist: false,
                //     create: false,
                //     valueField: 'id',
                //     labelField: 'nama',
                //     searchField: 'nama',
                //     preload: true,
                //     onInitialize: function() {
                //         var selectize = this;
                //         $.ajax({
                //             url: '{{ url('/param_payroll/cabang/list') }}',
                //             type: 'GET',
                //             dataType: 'json',
                //             success: function(res) {

                //                 selectize.addOption(res);
                //                 selectize.refreshOptions(
                //                     false); // Refresh options to show data immediately
                //             },
                //             error: function(err) {
                //                 console.log("Error loading data for 'cabang': ", err);
                //             }
                //         });
                //     }
                // });

                // // <!--Inisialisasi Selectize untuk 'bagian'-->
                // $('#bagian').selectize({
                //     plugins: ['remove_button'],
                //     persist: false,
                //     create: false,
                //     valueField: 'id',
                //     labelField: 'nama',
                //     searchField: 'nama',
                //     preload: true,
                //     onInitialize: function() {
                //         var selectize = this;
                //         $.ajax({
                //             url: '{{ url('/param_payroll/bagian/list') }}',
                //             type: 'GET',
                //             dataType: 'json',
                //             success: function(res) {

                //                 selectize.addOption(res);
                //                 selectize.refreshOptions(
                //                     false); // Refresh options to show data immediately
                //             },
                //             error: function(err) {
                //                 console.log("Error loading data for 'bagian': ", err);
                //             }
                //         });
                //     }
                // });

                // $('#level_jabatan').selectize({
                //     plugins: ['remove_button'],
                //     persist: false,
                //     create: false,
                //     valueField: 'id',
                //     labelField: 'nama',
                //     searchField: 'nama',
                //     preload: true,
                //     onInitialize: function() {
                //         var selectize = this;
                //         $.ajax({
                //             url: '{{ url('/param_payroll/jabatan/list') }}',
                //             type: 'GET',
                //             dataType: 'json',
                //             success: function(res) {

                //                 selectize.addOption(res);
                //                 selectize.refreshOptions(
                //                     false); // Refresh options to show data immediately
                //             },
                //             error: function(err) {
                //                 console.log("Error loading data for 'level_jabatan': ",
                //                     err);
                //             }
                //         });
                //     }
                // });

                // $('#par_level_jabatan').selectize({
                //     plugins: ['remove_button'],
                //     persist: false,
                //     create: false,
                //     valueField: 'id',
                //     labelField: 'nama',
                //     searchField: 'nama',
                //     preload: true,
                //     onInitialize: function() {
                //         var selectize = this;
                //         $.ajax({
                //             url: '{{ url('/param_payroll/department/list') }}',
                //             type: 'GET',
                //             dataType: 'json',
                //             success: function(res) {

                //                 selectize.addOption(res);
                //                 selectize.refreshOptions(
                //                     false); // Refresh options to show data immediately
                //             },
                //             error: function(err) {
                //                 console.log("Error loading data for 'par_level_jabatan': ",
                //                     err);
                //             }
                //         });
                //     }
                // });
            });
        }

        function loadEmployeeData() {
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

        function Reset() {
            loadEmployeeData();
        }

        function showComponent() {
            $.get("{{ url('/adjusment/component') }}", {},
                function(data, status) {
                    $("#pagemanage").html(data);
                    $('#exampleModal1').modal('show');

                    loadComponentData();

                });
        }

        function loadComponentData() {
            $.ajax({
                url: '{{ url('/componen/componen-list') }}',
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $('#comitems').empty();
                    totalComponents = res.length;
                    res.forEach(function(component) {
                        let label = component.nama + " - " + component.komponen;
                        $('#comitems').append('<option value="' +
                            component.id + '">' + label + '</option>');
                    });
                    updateViewComponentCount();
                },
                error: function(err) {
                    console.log("Error loading employees data: ", err);
                }
            });
        }

        function importComponent() {
            var formData = new FormData($('#importForm')[0]);
            var isEmpty = true;

            for (var value of formData.values()) {
                if (value) {
                    isEmpty = false;
                    break;
                }
            }

            if (isEmpty) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Form tidak boleh kosong. Silakan masukkan data yang diperlukan.',
                });
                return;
            }

            Swal.fire({
                title: 'Importing Component...',
                html: '<div class="progress"><div class="progress-bar" role="progressbar" style="width: 0%; background-color: red;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div></div>',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "/param_payroll/import",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {},
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else {
                        let errorMessages = response.errors ? response.errors.join('<br>') : response.message;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMessages,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON.message || 'Terjadi kesalahan saat melakukan impor.',
                    });
                }
            });
        }
    </script>

    <!--STORELOAD-->
    <script>
        // document.getElementById('gridCheck1').addEventListener('change', function() {
        //     if (this.checked) {
        //         document.getElementById('end_date_input').style.display = 'block';
        //     } else {
        //         document.getElementById('end_date_input').style.display = 'none';
        //     }
        // });

        document.addEventListener("DOMContentLoaded", function() {
            var storeTable = document.getElementById('store');
        });

        document.getElementById('search').addEventListener('input', function() {
            const searchQuery = this.value.toLowerCase();
            filterTableData(searchQuery);
        });

        let currentPage = 1;
        const rowsPerPage = 10;
        let tableData = [];

        function insertResponseDataIntoTable(responseData) {
            console.log('Response Data TO TABLE:', responseData);

            var tableBody = document.querySelector('#myTable tbody');

            tableBody.innerHTML = '';

            responseData.forEach(function(employee) {
                var row = document.createElement('tr');
                row.classList.add('isi');
                var nameCell = document.createElement('td');
                nameCell.classList.add('name');
                nameCell.textContent = employee['nama_lengkap'];
                row.appendChild(nameCell);

                var idCell = document.createElement('td');
                idCell.classList.add('id');
                idCell.textContent = employee['nomor_induk_karyawan'];
                row.appendChild(idCell);

                var idCell = document.createElement('td');
                idCell.classList.add('position');
                idCell.textContent = employee['jabatan'];
                row.appendChild(idCell);

                var selectedComponentCell = document.createElement('td');
                selectedComponentCell.setAttribute('colspan', '6');
                selectedComponentCell.textContent = 'Selected Component';
                selectedComponentCell.style.textAlign = 'center';
                selectedComponentCell.style.verticalAlign = 'middle';
                row.appendChild(selectedComponentCell);

                tableBody.appendChild(row);
            });
            document.getElementById('searchview').style.display = 'inline-block';
            document.getElementById('addComponentButton').disabled = false;
        }

        function populateComponentData(componentData) {
            tableData = [];

            componentData.forEach(function(employee, index) {
                employee.componentData.forEach(function(component, compIndex) {
                    tableData.push({
                        id: employee.id,
                        nama_lengkap: employee.nama_lengkap,
                        nomor_induk_karyawan: employee.nomor_induk_karyawan,
                        jabatan: employee.jabatan,
                        id_com: component.id_com,
                        component_name: component.nama,
                        component_type: component.komponen,
                        last_amount: component.nilai,
                        new_amount: "",
                        index: index,
                        compIndex: compIndex
                    });
                });
            });

            renderTableData();
        }

        function renderTableData(filteredData = tableData) {
            const tableBody = document.querySelector('#myTable tbody');
            tableBody.innerHTML = '';

            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;

            const paginatedData = filteredData.slice(startIndex, endIndex);

            let currentEmployeeIndex = null;

            paginatedData.forEach(function(item, index) {
                const row = tableBody.insertRow();
                row.classList.add('isi');

                if (item.index !== currentEmployeeIndex) {
                    currentEmployeeIndex = item.index;

                    const namaCell = row.insertCell();
                    namaCell.textContent = item.nama_lengkap;
                    namaCell.classList.add('name');

                    const idCell = row.insertCell();
                    idCell.textContent = item.nomor_induk_karyawan;
                    idCell.dataset.id = item.index;
                    idCell.classList.add('id');

                    const positionCell = row.insertCell();
                    positionCell.textContent = item.jabatan;
                    positionCell.classList.add('position');
                } else {
                    const namaCell = row.insertCell();
                    const idCell = row.insertCell();
                    const positionCell = row.insertCell();
                }

                const namaKomponenCell = row.insertCell();
                namaKomponenCell.textContent = item.component_name;
                namaKomponenCell.classList.add('komponen-name');

                const jenisKomponenCell = row.insertCell();
                jenisKomponenCell.textContent = item.component_type;
                jenisKomponenCell.classList.add('komponen-type');

                const jumlahSekarangCell = row.insertCell();
                jumlahSekarangCell.textContent = item.last_amount;
                jumlahSekarangCell.classList.add('last-amount');

                const jumlahBaruCell = row.insertCell();

                const inputGroupDiv = document.createElement('div');
                inputGroupDiv.classList.add('input-group', 'mb-3');

                const inputElement = document.createElement('input');
                inputElement.setAttribute('type', 'number');
                inputElement.classList.add('form-control');
                inputElement.setAttribute('placeholder', 'New amount');
                inputElement.setAttribute('aria-label', 'New amount');
                inputElement.setAttribute('id', 'new-amount-' + item.index + '-' + item.compIndex);
                inputElement.value = item.new_amount;

                inputElement.addEventListener('input', function() {
                    item.new_amount = inputElement.value;
                });

                inputGroupDiv.appendChild(inputElement);
                jumlahBaruCell.appendChild(inputGroupDiv);

                const deleteButtonCell = row.insertCell();
                deleteButtonCell.classList.add('delete-button');

                const deleteButtonSVG = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                deleteButtonSVG.setAttribute("xmlns", "http://www.w3.org/2000/svg");
                deleteButtonSVG.setAttribute("width", "16");
                deleteButtonSVG.setAttribute("height", "16");
                deleteButtonSVG.setAttribute("fill", "currentColor");
                deleteButtonSVG.setAttribute("class", "bi bi-x-circle");
                deleteButtonSVG.setAttribute("viewBox", "0 0 16 16");
                deleteButtonSVG.innerHTML =
                    '<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>' +
                    '<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>';

                deleteButtonSVG.addEventListener('click', function() {
                    deleteComponent(item.index, item.compIndex);
                });

                deleteButtonCell.appendChild(deleteButtonSVG);
            });

            renderPaginationControls(filteredData);
        }

        function deleteComponent(employeeIndex, componentIndex) {
            const employee = tableData.filter(item => item.index === employeeIndex);
            const updatedComponents = employee.filter(item => item.compIndex !== componentIndex);

            tableData = tableData.filter(item => !(item.index === employeeIndex && item.compIndex === componentIndex));

            const remainingComponents = tableData.filter(item => item.index === employeeIndex);
            if (remainingComponents.length === 0) {
                tableData = tableData.filter(item => item.index !== employeeIndex);
            }

            renderTableData();
        }

        function storeParamPayroll() {

            var employeedata = [];

            tableData.forEach(function(rowData) {
                var employee = employeedata.find(function(e) {
                    return e.nomor_induk_karyawan === rowData.nomor_induk_karyawan;
                });

                if (!employee) {
                    employee = {
                        id: rowData.id,
                        nomor_induk_karyawan: rowData.nomor_induk_karyawan,
                        nama_lengkap: rowData.nama_lengkap,
                        jabatan: rowData.jabatan,
                        components: []
                    };
                    employeedata.push(employee);
                }

                employee.components.push({
                    id_com: rowData.id_com,
                    component_name: rowData.component_name,
                    component_type: rowData.component_type,
                    last_amount: rowData.last_amount,
                    new_amount: rowData.new_amount ||
                        null
                });
            });



            var data = {
                type: $("input[name='type']:checked").val(),
                efektif_date: $("input[name='efektif_date']").val(),
                description: $("textarea[name='description']").val(),
                end_date: $("input[name='end_date']").val(),
                employeedata: employeedata
            };

            console.log('Data KIRIM PARAMPAYROLL :', data);

            $.ajax({
                url: '{{ url('adjusment/store-adjusment') }}',
                method: 'POST',
                data: JSON.stringify(data),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        icon: "success",
                        title: response.message,
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: "Ok",
                        timer: 1500,
                    }).then((result) => {
                        if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
                            window.location.href = '/pra-payroll';
                        }
                    });
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    if (xhr.status === 400) {
                        var errorObject = xhr.responseJSON.error;
                        var errorMessage = 'Check Input : ';
                        for (var key in errorObject) {
                            if (errorObject.hasOwnProperty(key)) {
                                errorMessage += '(' + key + ') ' + errorObject[key] + '\n';
                            }
                        }
                        Swal.fire({
                            icon: "error",
                            title: "Validation Error",
                            text: errorMessage,
                        });
                    } else if (xhr.status === 500) {
                        var serverErrorMessage = xhr.responseJSON.error;
                        Swal.fire({
                            icon: "error",
                            title: "Server Error",
                            text: serverErrorMessage,
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Server Error",
                            text: "An error occurred on the server. Please try again later.",
                        });
                    }
                    console.error(error);
                }
            });
        }

        function filterTableData(searchQuery) {
            const searchQueryLower = searchQuery.toLowerCase();

            const filteredData = tableData.filter(function(data) {
                const name = data.nama_lengkap ? data.nama_lengkap.toLowerCase() : '';
                const id = data.nomor_induk_karyawan ? data.nomor_induk_karyawan.toString().toLowerCase() : '';
                const position = data.jabatan ? data.jabatan.toLowerCase() : '';

                return (name.includes(searchQueryLower) || id.includes(searchQueryLower) || position.includes(
                    searchQueryLower));
            });

            currentPage = 1;

            renderTableData(filteredData);
            renderPaginationControls(filteredData);
        }

        function renderPaginationControls(filteredData = tableData) {
            const paginationControls = document.getElementById('paginationControls');
            paginationControls.innerHTML = '';

            const totalPages = Math.ceil(filteredData.length / rowsPerPage);
            const maxButtons = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);

            if (endPage - startPage + 1 < maxButtons) {
                startPage = Math.max(1, endPage - maxButtons + 1);
            }

            const prevButton = document.createElement('button');
            prevButton.classList.add('page-btn');
            prevButton.textContent = '<';
            prevButton.disabled = (currentPage === 1);
            prevButton.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    renderTableData(filteredData);
                }
            });
            paginationControls.appendChild(prevButton);

            for (let i = startPage; i <= endPage; i++) {
                const paginationButton = document.createElement('button');
                paginationButton.classList.add('page-btn');
                paginationButton.textContent = i;

                if (i === currentPage) {
                    paginationButton.classList.add('active');
                }

                paginationButton.addEventListener('click', function() {
                    currentPage = i;
                    renderTableData(filteredData);
                });

                paginationControls.appendChild(paginationButton);
            }

            const nextButton = document.createElement('button');
            nextButton.classList.add('page-btn');
            nextButton.textContent = '>';
            nextButton.disabled = (currentPage === totalPages);
            nextButton.addEventListener('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderTableData(filteredData);
                }
            });
            paginationControls.appendChild(nextButton);
        }
    </script>



    <!-- Modal SELECTKARR -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <div class="modal fade modselect" id="exampleModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                    <div id="pageemploy" class="p-2"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal SELECTCOM -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <div class="modal fade modselect" id="exampleModal1" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Select Component</h5>
                    <button type="button" class="close" onClick="Close()" id="close-button" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="pagemanage" class="p-2"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
