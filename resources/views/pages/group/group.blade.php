@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Group'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6></h6>
                    <button type="button" class="btn btn-primary" onclick="createGroup()">
                        Tambah Group
                    </button>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="container">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="groupTable">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            No
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Name</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Code</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Project</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Kepala Group</th>
                                        <th
                                            class="text-uppercase text-center text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index = 1; @endphp
                                    @foreach ($groups as $g)
                                        <tr>
                                            <td class="align-middle text-center">
                                                <p class="text-sm font-weight-bold mb-0">
                                                    {{ $index++ }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $g->name }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $g->code }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $g->project->name }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $g->leader->name }}</p>
                                            </td>

                                            <td class="align-middle text-end">
                                                <div class="d-flex px-3 py-1 justify-content-center align-items-center">
                                                    <button type="button" class="btn btn-link text-primary mb-0"
                                                        onclick="listGroup({{ $g->id }})">
                                                        Detail
                                                    </button>
                                                    <button type="button" class="btn btn-link text-primary mb-0"
                                                        onclick="editGroup({{ $g->id }})">
                                                        Edit
                                                    </button>
                                                    <button type="button" class="btn btn-link text-danger mb-0"
                                                        data-bs-toggle="modal" data-bs-target="#deleteRoleModal"
                                                        data-id="{{ $g->id }}">
                                                        Delete
                                                    </button>
                                                </div>
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

    <!---Modal Create-->
    <div class="modal fade" id="addGroupModal" tabindex="-1" aria-labelledby="addGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGroupModalLabel">Tambah Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="creategroup"></div>
                </div>
            </div>
        </div>
    </div>

    <!---Modal Edit-->
    <div class="modal fade" id="EditGroupModal" tabindex="-1" aria-labelledby="EditGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="EditGroupModalLabel">Tambah Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editgroup"></div>
                </div>
            </div>
        </div>
    </div>

  <!-- Modal -->
<div class="modal fade" id="EditGroupList" tabindex="-1" aria-labelledby="EditGroupListLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="EditGroupListLabel">Detail Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <table class="table table-bordered table-striped" id="groupTableDetail">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>NIK</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Position</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody id="groupMemberList">
                        <!-- Data karyawan akan dimasukkan di sini -->
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>


    <script>
         $(document).ready(function() {
        $('#groupTableDetail').DataTable({
            responsive: true,
        });
    });
        $(document).ready(function() {
            $('#groupTable').DataTable({
                responsive: true,
                pageLength: 5,
                pagingType: 'simple_numbers',
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Tidak ada entri",
                    zeroRecords: "Tidak ada entri yang cocok",
                    // paginate: {
                    //     first: "Pertama",
                    //     last: "Terakhir",
                    //     next: "Berikutnya",
                    //     previous: "Sebelumnya"
                    // }
                }
            });
        });
        function createGroup() {
            $.ajax({
                url: "{{ url('/group/create') }}",
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    $("#creategroup").html(data);
                    $('#addGroupModal').modal('show');
                    $(document).ready(function() {
                        $('#project').selectize({
                            placeholder: 'Select Project',
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            preload: true,
                            load: function(query, callback) {

                                $.ajax({
                                    url: '/project/list',
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
                        $('#leader').selectize({
                            placeholder: 'Select Leader',
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            preload: true,
                            load: function(query, callback) {
                                $.ajax({
                                    url: '/employee/list/kepala-pekerja',
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
                        $('#members').selectize({
                            placeholder: 'Pilih Tukang',
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            preload: true,
                            plugins: [
                                'remove_button'
                            ], // Allows items to be removed
                            load: function(query, callback) {
                                $.ajax({
                                    url: '/employee/list/pekerja', // Adjust URL if necessary
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
                            },
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

        function listGroup(id) {
    $.ajax({
        url: "{{ url('/group/list') }}/" + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            // Kosongkan daftar sebelumnya
            $('#groupMemberList').empty();

            // Tambahkan data ke tabel
            let employees = response.dataEmployes;
            let positions = response.dataPosition;
            employees.forEach((employee, index) => {
                let positionName = positions.find(position => position.id === employee.position_id)?.name || 'Tidak Diketahui';

                $('#groupMemberList').append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>${employee.name}</td>
                        <td>${employee.nip}</td>
                        <td>${employee.nik}</td>
                        <td>${employee.address}</td>
                        <td>${employee.phone}</td>
                        <td>${positionName}</td>
                        <td>${employee.email}</td>
                        
                    </tr>
                `);
            });

            // Tampilkan modal
            $('#EditGroupList').modal('show');
        },
        error: function() {
            alert('Terjadi kesalahan saat mengambil data.');
        }
    });
}

        function editGroup(id) {
            $.ajax({
                url: "{{ url('/group/edit') }}/" + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $("#editgroup").html(response.html);
                    $('#EditGroupModal').modal('show');
                    const existingProjectId = response.project_id
                    const existingLeaderId = response.leader_id

                    $('#projectedit').selectize({
                        placeholder: 'Select Project',
                        valueField: 'id',
                        labelField: 'name',
                        searchField: 'name',
                        preload: true,
                        load: function(query, callback) {

                            $.ajax({
                                url: '/project/list',
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
                        },
                        onInitialize: function() {
                            const selectize = this;

                            if (existingProjectId) {
                                $.ajax({
                                    url: '/project/get-project-name',
                                    type: 'GET',
                                    dataType: 'json',
                                    data: {
                                        project_id: existingProjectId
                                    },
                                    success: function(data) {
                                        selectize.addOption({
                                            id: existingProjectId,
                                            name: data.name
                                        });
                                        selectize.setValue(
                                            existingProjectId);
                                    }
                                });
                            }
                        }
                    });
                    $('#leaderedit').selectize({
                        placeholder: 'Select Leader',
                        valueField: 'id',
                        labelField: 'name',
                        searchField: 'name',
                        preload: true,
                        load: function(query, callback) {
                            $.ajax({
                                url: '/employee/list',
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
                        },
                        onInitialize: function() {
                            const selectize = this;

                            if (existingLeaderId) {
                                $.ajax({
                                    url: '/employee/get-employee-name',
                                    type: 'GET',
                                    dataType: 'json',
                                    data: {
                                        leader_id: existingLeaderId
                                    },
                                    success: function(data) {
                                        selectize.addOption({
                                            id: existingLeaderId,
                                            name: data.name
                                        });
                                        selectize.setValue(
                                            existingLeaderId);
                                    }
                                });
                            }
                        }
                    });
                    $(document).ready(function() {
                        $('#members').selectize({
                            placeholder: 'Pilih Tukang',
                            valueField: 'id',
                            labelField: 'name',
                            searchField: 'name',
                            preload: true,
                            plugins: ['remove_button'], // Allows items to be removed
                            load: function(query, callback) {
                                $.ajax({
                                    url: '/employee/list/pekerja', // Adjust URL if necessary
                                    type: 'GET',
                                    dataType: 'json',
                                    data: {
                                        q: query
                                    },
                                    success: function(data) {
                                        console.log('cek : ')
                                        callback(data);
                                    },
                                    error: function() {
                                        callback();
                                    }
                                });
                            },
                            onInitialize: function() {
                                const selectize = this;

                                if (existingLeaderId) {
                                    $.ajax({
                                        url: '/employee/get-employee-name',
                                        type: 'GET',
                                        dataType: 'json',
                                        data: {
                                            leader_id: existingLeaderId
                                        },
                                        success: function(data) {
                                            selectize.addOption({
                                                id: existingLeaderId,
                                                name: data.name
                                            });
                                            selectize.setValue(
                                                existingLeaderId);
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
                        text: 'Failed to open Edit Group form. Please try again later.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        async function StoreGroup() {
    event.preventDefault();

    const submitButton = document.getElementById('btn-submit');
    submitButton.disabled = true;

    // Ambil nilai manual dari elemen form
    const name = document.getElementById('name').value;
    const code = document.getElementById('code').value;
    const project = document.getElementById('project').value;
    const leader = document.getElementById('leader').value;

    // Ambil nilai dari Selectize
    const membersSelect = $('#members')[0].selectize;
    const members = membersSelect.getValue(); // Array dari anggota yang dipilih

    // Validasi jika tidak ada anggota yang dipilih
    if (members.length === 0) {
        Swal.fire('Error', 'Harap pilih minimal satu anggota.', 'error');
        submitButton.disabled = false;
        return;
    }

    Swal.fire({
        title: 'Menyimpan data...',
        html: 'Progress: <b>0%</b>',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Susun data secara manual
    const formData = {
        name: name,
        code: code,
        project: project,
        leader: leader,
        members: members, // Array anggota
    };

    console.log('Data yang dikirim:', formData);

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

        const response = await fetch('/group/store', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData),
        });

        clearInterval(progressInterval);

        const responseData = await response.json();
        console.log('Response Data:', responseData); // Cek respons dari server

        // Pastikan responsData memiliki key success atau error
        if (!response.ok || !responseData.success) {
            // Jika tidak ada response.ok atau success, ambil pesan error
            const errorMessage = responseData.error || responseData.message || 'Terjadi kesalahan';
            throw new Error(errorMessage); 
        }

        // Jika sukses
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: responseData.message || 'Data berhasil disimpan'
        }).then(() => {
            location.reload();
        });

    } catch (error) {
        console.log('Caught error:', error); // Log error yang ditangkap
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: error.message || 'Gagal Menyimpan Data'
        });
    } finally {
        submitButton.disabled = false;
    }
}

    </script>
@endsection
