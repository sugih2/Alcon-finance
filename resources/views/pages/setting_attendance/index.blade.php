@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Setting Attendance'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="container">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-3" id="settingAttendanceTable">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            No.
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Minimal Minutes
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Maximal Minutes</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Deduction Type</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Deduction Value</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index = 1; @endphp
                                    @foreach ($setattendance as $s)
                                        <tr>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $index++ }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $s->min_minutes }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $s->max_minutes }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $s->deduction_type }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $s->deduction_value }}</p>
                                            </td>
                                            <td class="align-middle text-end">
                                                <div class="d-flex px-3 py-1 justify-content-center align-items-center">
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#editModal"
                                                        onclick="editSetAttendance({{ $s->id }})">
                                                        Setting
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
    <!--Modal Shift--->
    <div class="modal fade" id="EditShiftModal" tabindex="-1" aria-labelledby="editPositionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editShiftModalLabel">Setting Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editShift"></div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Shift End --}}

    <script>
        $(document).ready(function() {
            $('#settingAttendanceTable').DataTable({
                responsive: true,
            });
        });

        function editSetAttendance(id) {
            fetch(`/shifts/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.data) {
                        document.getElementById('min_minutes').value = data.data.min_minutes;
                        document.getElementById('max_minutes').value = data.data.max_minutes;
                        document.getElementById('deduction_type').value = data.data.deduction_type;
                        document.getElementById('deduction_value').value = data.data.deduction_value;

                        const form = document.getElementById('editShiftForm');
                        form.onsubmit = function(e) {
                            e.preventDefault();
                            updateShift(id, new FormData(form));
                        };
                    }
                })
                .catch(error => console.error('Error fetching shift:', error));
        }

        function updateShift(id, formData) {
            fetch(`/shifts/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert(data.message);
                        location.reload();
                    }
                })
                .catch(error => console.error('Error updating shift:', error));
        }
    </script>
@endsection
