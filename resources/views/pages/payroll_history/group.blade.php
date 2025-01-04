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

                            <table class="table table-striped table-hover align-items-center mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Group Name</th>
                                        <th>Group Code</th>
                                        <th class="text-center">Leader</th>
                                        <th class="text-right">Total Salary</th>
                                        <th class="text-right">Total Allowance</th>
                                        <th class="text-right">Total Deduction</th>
                                        <th class="text-right">Gross Salary</th>
                                        <th class="text-right">Net Salary</th>
                                        <th class="text-center">Action</th>
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
                                                {{ number_format($group['gross_salary'], 0, ',', '.') }}</td>
                                            <td class="text-right">Rp.
                                                {{ number_format($group['net_salary'], 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <button class="btn btn-primary btn-sm"
                                                    onclick="window.location.href='{{ route('historypayrollDetail.index', ['payrollHistoryId' => $payrollHistoryDetail->id, 'groupId' => $group['groupid']]) }}'">
                                                    Detail Employee
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

    <!-- Modal Edit -->
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
    </script>
@endsection
