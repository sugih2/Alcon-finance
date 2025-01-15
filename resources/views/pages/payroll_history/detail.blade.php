@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Payroll History Employee'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Payroll History Employee</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="container">
                        <div class="table-responsive p-0">

                            <table class="table">
                                <tr>
                                    <th>Group Name</th>
                                    <td>{{ $result['group_name'] }}</td>
                                </tr>
                                <tr>
                                    <th>Group Code</th>
                                    <td>{{ $result['group_code'] }}</td>
                                </tr>
                                <tr>
                                    <th>Leader</th>
                                    <td>
                                        @if ($result['leader'])
                                            {{ $result['leader']['name'] }} (NIP: {{ $result['leader']['nip'] }})
                                            <br>
                                            <strong>Payroll Details:</strong>
                                            <div class="row">
                                                <div class="col">
                                                    <strong>Salary:</strong> Rp.
                                                    {{ number_format($result['leader']['payroll_details'][0]['salary'], 0, ',', '.') }}
                                                </div>
                                                <div class="col">
                                                    <strong>Total Incomes:</strong> Rp.
                                                    {{ number_format($result['leader']['payroll_details'][0]['total_pendapatan'], 0, ',', '.') }}
                                                </div>
                                                <div class="col">
                                                    <strong>Total Overtime:</strong> Rp.
                                                    {{ number_format($result['leader']['payroll_details'][0]['total_overtime'], 0, ',', '.') }}
                                                </div>
                                                <div class="col">
                                                    <strong>Gross Salary:</strong> Rp.
                                                    {{ number_format($result['leader']['payroll_details'][0]['gaji_bruto'], 0, ',', '.') }}
                                                </div>
                                                <div class="col">
                                                    <strong>Net Salary:</strong> Rp.
                                                    {{ number_format($result['leader']['payroll_details'][0]['gaji_bersih'], 0, ',', '.') }}
                                                </div>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </table>


                            <table class="table align-items-center mb-0" id="PayrollHistoryDetailTablee">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            No</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Member Name</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Member NIP</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Salary</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Allowance</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Deduction</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Total Incomes</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Total Overtime</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Total Deductions</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Gross Salary</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Net Salary</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index = 1; @endphp
                                    @foreach ($result['members'] as $member)
                                        @php
                                            $payrollDetails = $member['payroll_details'];
                                            $rowSpan = count($payrollDetails) > 0 ? count($payrollDetails) : 1;
                                        @endphp
                                        <tr>
                                            <td rowspan="{{ $rowSpan }}">{{ $index++ }}</td>
                                            <td rowspan="{{ $rowSpan }}">{{ $member['employee_name'] }}</td>
                                            <td rowspan="{{ $rowSpan }}">{{ $member['employee_nip'] }}</td>

                                            @if (!empty($payrollDetails))
                                                @foreach ($payrollDetails as $key => $payroll)
                                                    @if ($key > 0)
                                        <tr>
                                    @endif
                                    <td>Rp. {{ number_format($payroll['salary'], 0, ',', '.') }}</td>
                                    <td>
                                        @if (!empty($payroll['allowance']))
                                            <ul>
                                                @foreach ($payroll['allowance'] as $allowance)
                                                    <li>{{ $allowance['nama'] }}: Rp.
                                                        {{ number_format($allowance['nilai'], 0, ',', '.') }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if (!empty($payroll['deduction']))
                                            <ul>
                                                @foreach ($payroll['deduction'] as $deduction)
                                                    <li>{{ $deduction['nama'] }}: Rp.
                                                        {{ number_format($deduction['nilai'], 0, ',', '.') }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>Rp. {{ number_format($payroll['total_pendapatan'], 0, ',', '.') }}</td>
                                    <td>Rp. {{ number_format($payroll['total_overtime'], 0, ',', '.') }}</td>
                                    <td>Rp. {{ number_format($payroll['total_potongan'], 0, ',', '.') }}</td>
                                    <td>Rp. {{ number_format($payroll['gaji_bruto'], 0, ',', '.') }}</td>
                                    <td>Rp. {{ number_format($payroll['gaji_bersih'], 0, ',', '.') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-link text-primary mb-0" data-bs-toggle="modal"
                                            data-bs-target="#editModal" onclick="detailAttendance({{ $member['id'] }})">
                                            Show Attendance
                                        </button>
                                    </td>
                                    @if ($key > 0)
                                        </tr>
                                    @endif
                                    @endforeach
                                @else
                                    <td colspan="8" class="text-center">No Payroll Details</td>
                                    @endif
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
            $('#PayrollHistoryDetailTablee').DataTable({
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
