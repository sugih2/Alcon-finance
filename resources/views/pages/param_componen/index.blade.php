@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Param Componen'])
    @include('sweetalert::alert')
    <style>
        .custom-dropdown-menu {
            display: none;
            position: absolute;
            z-index: 1050;
            background: #ffffff;
            border: 1px solid #ccc;
            padding: 10px 0;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-width: 200px;
        }

        .custom-dropdown-menu.show {
            display: block;
        }

        .custom-dropdown-item {
            padding: 10px 15px;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            display: block;
        }

        .custom-dropdown-item:hover {
            background-color: #f8f9fa;
            color: #007bff;
        }

        .border-form {
            border: 1px solid #B5B5B5;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .border-form .headform {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            padding: 10px 0;
            margin-bottom: 20px;
            border-radius: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ccc;
        }

        .border-form .subtitle {
            margin-bottom: 10px;
        }

        .border-form .subtitle h6 {
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
            padding-bottom: 5px;
            border-bottom: 2px solid #ccc;
            display: inline-block;
        }

        .border-form .select {
            border-bottom: 1px solid #B5B5B5;
        }
    </style>

    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6></h6>
                    <div class="dropdown mb-3">
                        <button class="btn btn-primary" type="button" id="customDropdownButton" style="border-radius: 5px;">
                            Add Component
                        </button>
                        <div id="customDropdownMenu" class="custom-dropdown-menu">
                            <a href="#" class="custom-dropdown-item" onclick="loadForm('salary')">Salary</a>
                            <a href="#" class="custom-dropdown-item" onclick="loadForm('allowance')">Allowance</a>
                            <a href="#" class="custom-dropdown-item" onclick="loadForm('deduction')">Deduction</a>
                        </div>

                    </div>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="container">
                        <div class="table-responsive p-0">
                            <div id="readcreatcom" class=""></div>
                            <table class="table align-items-center mb-0" id="componenTable">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            No
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Name
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Componen
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Amount</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index = 1; @endphp
                                    @foreach ($componens as $c)
                                        <tr>
                                            <td class="align-middle text-center">{{ $index++ }}</td>
                                            <td>{{ $c->name }}</td>
                                            <td>{{ $c->componen }}</td>
                                            <td>{{ $c->amount }}</td>
                                            <td class="align-middle text-end">
                                                <button type="button" class="btn btn-link text-primary mb-0"
                                                    onclick="editComponen({{ $c->id }})">Edit</button>
                                                <button type="button" class="btn btn-link text-danger mb-0"
                                                    data-bs-toggle="modal" data-bs-target="#deleteRoleModal"
                                                    data-id="{{ $c->id }}">Delete</button>
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
    <div class="modal fade" id="addComponenModal" tabindex="-1" aria-labelledby="addComponenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addComponenModalLabel">Tambah Componen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="createComponen"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="EditComponenModal" tabindex="-1" aria-labelledby="EditComponenModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="EditComponenModalLabel">Edit Componen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editComponen"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#componenTable').DataTable({
                responsive: true,
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const dropdownButton = document.getElementById("customDropdownButton");
            const dropdownMenu = document.getElementById("customDropdownMenu");

            dropdownButton.addEventListener("click", function(event) {
                event.stopPropagation();
                dropdownMenu.classList.toggle("show");
            });

            dropdownMenu.addEventListener("click", function(event) {
                event.stopPropagation();
            });

            document.addEventListener("click", function() {
                dropdownMenu.classList.remove("show");
            });
        });

        function loadForm(componentType) {
            $.ajax({
                url: "{{ url('/componen/getform') }}/" + componentType,
                type: 'GET',
                success: function(response) {
                    $('#readcreatcom').html(response);
                    $('.headform').text(componentType.charAt(0).toUpperCase() + componentType.slice(1));
                    const dropdownMenu = document.getElementById("customDropdownMenu");
                    if (dropdownMenu.classList.contains("show")) {
                        dropdownMenu.classList.remove("show");
                    }
                }
            });
        }

        function createComponen() {
            $.ajax({
                url: "{{ url('/componen/create') }}",
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    $("#createComponen").html(data);
                    $('#addComponenModal').modal('show');
                    $(document).ready(function() {

                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to open create Componen form. Please try again later.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        function createallowance() {
            event.preventDefault();

            var selectedPosition = $('#id_regency').val();
            var cityName = $('#id_regency option:selected').text();
            var selectedCategory = $('input[name="category"]:checked').next('label').text().trim().replace(/\s+/g, ' ');
            if (!selectedPosition) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select Position.',
                });
                return false;
            }
            if (!selectedCategory) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select a category.',
                });
                return false;
            }
            var formattedName = `Tunjangan ${selectedCategory} ${cityName}`;
            var rawValue = $('#nilai').val().replace(/\./g, '');
            $('#nilai').val(rawValue);
            var form = $('#allowanceForm')[0];
            var formData = new FormData(form);
            formData.append('name', formattedName);

            formData.forEach(function(value, key) {
                console.log(`${key}: ${value}`);
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ url('/componen/store') }}",
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function(data, textStatus, xhr) {
                    Swal.fire({
                        icon: 'success',
                        title: data.message,
                        confirmButtonText: 'OK',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#closeButton').trigger('click');
                        }
                    });
                },
                error: function(xhr, textStatus, errorThrown) {
                    var errorMessage = "Error occurred while processing your request. Please try again later.";

                    if (xhr.status === 422) {
                        var response = xhr.responseJSON;

                        if (response.message) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: response.message,
                            });
                        } else if (response.errors) {
                            var errorList = '';
                            $.each(response.errors, function(key, value) {
                                errorList += '<li>' + value[0] + '</li>';
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Errors',
                                html: '<ul>' + errorList + '</ul>',
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                        });
                    }
                },
            });
        }

        function createsalary() {
            event.preventDefault();

            var selectedPosition = $('#id_position').val();
            if (!selectedPosition) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select Position.',
                });
                return false;
            }

            var rawValue = $('#nilai').val().replace(/\./g, '');
            $('#nilai').val(rawValue);
            var form = $('#salaryForm')[0];
            var formData = new FormData(form);

            formData.forEach(function(value, key) {
                console.log(`${key}: ${value}`);
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ url('/componen/store') }}",
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function(data, textStatus, xhr) {
                    Swal.fire({
                        icon: 'success',
                        title: data.message,
                        confirmButtonText: 'OK',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#closeButton').trigger('click');
                        }
                    });
                },
                error: function(xhr, textStatus, errorThrown) {
                    var errorMessage = "Error occurred while processing your request. Please try again later.";

                    if (xhr.status === 422) {
                        var response = xhr.responseJSON;

                        if (response.message) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: response.message,
                            });
                        } else if (response.errors) {
                            var errorList = '';
                            $.each(response.errors, function(key, value) {
                                errorList += '<li>' + value[0] + '</li>';
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Errors',
                                html: '<ul>' + errorList + '</ul>',
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                        });
                    }
                },
            });
        }

        function creatededuction() {
            event.preventDefault();

            var rawValue = $('#nilai').val().replace(/\./g, '');
            $('#nilai').val(rawValue);
            var form = $('#deductionForm')[0];
            var formData = new FormData(form);

            formData.forEach(function(value, key) {
                console.log(`${key}: ${value}`);
            });

            // $.ajaxSetup({
            //     headers: {
            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //     }
            // });

            // $.ajax({
            //     url: "{{ url('/componen/store') }}",
            //     data: formData,
            //     cache: false,
            //     processData: false,
            //     contentType: false,
            //     type: 'POST',
            //     success: function(data, textStatus, xhr) {
            //         Swal.fire({
            //             icon: 'success',
            //             title: data.message,
            //             confirmButtonText: 'OK',
            //         }).then((result) => {
            //             if (result.isConfirmed) {
            //                 $('#closeButton').trigger('click');
            //             }
            //         });
            //     },
            //     error: function(xhr, textStatus, errorThrown) {
            //         var errorMessage = "Error occurred while processing your request. Please try again later.";

            //         if (xhr.status === 422) {
            //             var response = xhr.responseJSON;

            //             if (response.message) {
            //                 Swal.fire({
            //                     icon: 'error',
            //                     title: 'Validation Error',
            //                     text: response.message,
            //                 });
            //             } else if (response.errors) {
            //                 var errorList = '';
            //                 $.each(response.errors, function(key, value) {
            //                     errorList += '<li>' + value[0] + '</li>';
            //                 });

            //                 Swal.fire({
            //                     icon: 'error',
            //                     title: 'Validation Errors',
            //                     html: '<ul>' + errorList + '</ul>',
            //                 });
            //             }
            //         } else {
            //             Swal.fire({
            //                 icon: 'error',
            //                 title: 'Error',
            //                 text: errorMessage,
            //             });
            //         }
            //     },
            // });
        }

        async function StoreComponen() {
            event.preventDefault();

            const form = document.getElementById('FormComponen');
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
