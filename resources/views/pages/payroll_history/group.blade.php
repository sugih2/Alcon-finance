@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Payroll History Group'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Payroll History Detail Group</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="container">
                        <div class="table-responsive p-0">

                            <table class="table">
                                <tr>
                                    <th>ID Transaksi</th>
                                    <td>{{ $payrollHistoryDetail->id_transaksi_payment }}</td>
                                </tr>
                                <tr>
                                    <th>Periode</th>
                                    <td>{{ $payrollHistoryDetail->start_periode }} -
                                        {{ $payrollHistoryDetail->end_periode }}</td>
                                </tr>
                                <tr>
                                    <th>Total Karyawan</th>
                                    <td>{{ $payrollHistoryDetail->total_karyawan }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Transaksi</th>
                                    <td>Rp. {{ number_format($payrollHistoryDetail->amount_transaksi, 0, ',', '.') }}</td>
                                </tr>
                            </table>

                            <table class="table align-items-center mb-0" id="PayrollHistoryDetailTable">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            No</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Group Name</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Group Code</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Leader</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Total Salary</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Total Allowance</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Total Deduction</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Total Overtime</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Gross Salary</th>

                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Net Salary</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Deduction Group</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Nett Salary Group</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index = 1; @endphp
                                    @foreach ($result as $group)
                                        <tr>
                                            <td class="text-center">{{ $index++ }}</td>
                                            <td>{{ $group['group_name'] }}</td>
                                            <td>{{ $group['group_code'] }}</td>
                                            <td class="text-center">{{ $group['leader'] ?? '-' }}</td>
                                            <td class="text-right">Rp.
                                                {{ number_format($group['total_salary'], 0, ',', '.') }}</td>
                                            <td class="text-right">Rp.
                                                {{ number_format($group['total_allowance'], 0, ',', '.') }}</td>
                                            <td class="text-right">Rp.
                                                {{ number_format($group['total_deduction'], 0, ',', '.') }}</td>
                                            <td class="text-right">Rp.
                                                {{ number_format($group['total_overtime'], 0, ',', '.') }}</td>
                                            <td class="text-right">Rp.
                                                {{ number_format($group['gross_salary'], 0, ',', '.') }}</td>
                                            <td class="text-right">Rp.
                                                {{ number_format($group['net_salary'], 0, ',', '.') }}</td>
                                            <td class="text-right">Rp.
                                                {{ number_format($group['deduction_group_total'], 0, ',', '.') }}</td>
                                            <td class="text-right">Rp.
                                                {{ number_format($group['net_salary_after_deduction_group'], 0, ',', '.') }}
                                            </td>

                                            <td class="text-center">
                                                <button class="btn btn-info btn-sm"
                                                    onclick="window.location.href='{{ route('historypayrollDetail.index', ['payrollHistoryId' => $payrollHistoryDetail->id, 'groupId' => $group['groupid']]) }}'">
                                                    Detail Employee
                                                </button>
                                                <button class="btn btn-primary btn-sm"
                                                    onclick="deductionGroup({{ $group['groupid'] }}, {{ $payrollHistoryDetail->id }})">
                                                    Deduction Group
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

    <!-- Modal Attendance Detail -->
    <div class="modal fade" id="DetailAttendanceModal" tabindex="-1" aria-labelledby="DetailAttendanceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="DetailPayrollModalLabel">Attendance Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="CloseDetail"></button>
                </div>
                <div class="modal-body">
                    <div id="detailAttendance"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Deduction Group -->
    <div class="modal fade" id="DeductionGroupModal" tabindex="-1" aria-labelledby="DeductionGroupModalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="DeductionGroupModalModalLabel">Deduction Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="CloseDetail"></button>
                </div>
                <div class="modal-body">
                    <div id="DeductionGroup"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#PayrollHistoryDetailTable').DataTable({
                responsive: true,
            });
        });

        function detailAttendance(id) {
            $.ajax({
                url: "{{ url('/history-payroll-detail/detail-attendance') }}/" + id,
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    $("#detailAttendance").html(data);
                    $('#DetailAttendanceModal').modal('show');
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

        function deductionGroup(groupId, payrollId) {
            $.ajax({
                url: "{{ url('/history-payroll-group/deduction-group') }}/" + groupId + "/" + payrollId,
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    $("#DeductionGroup").html(data);
                    $('#DeductionGroupModal').modal('show');
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

        function storeDeductionGroup() {
            event.preventDefault();

            var rawValue = $('#amount').val().replace(/\./g, '');
            $('#amount').val(rawValue);
            var form = $('#deductionGroupForm')[0];
            var formData = new FormData(form);

            var $button = $('#btn-deduction-group');
            $button.prop('disabled', true);

            formData.forEach(function(value, key) {
                console.log(`${key}: ${value}`);
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ url('/history-payroll-group/deduction-group-store') }}",
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
                            location.reload();
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
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        }
    </script>
@endsection
