<!-- Informasi Header -->
<div class="card mb-3">
    <div class="card-body">
        <h5>Informasi Payroll</h5>
        <div class="row">
            <div class="col-md-4">
                <strong>Nama Karyawan:</strong> {{ $payrollHistoryDetail->employee->name ?? '-' }}<br>
                <strong>ID Karyawan:</strong> {{ $payrollHistoryDetail->employee->nip ?? '-' }}<br>
                <strong>Gaji Pokok:</strong> {{ number_format($payrollHistoryDetail->salary, 2) }}
            </div>
            <div class="col-md-4">
                <strong>Total Tunjangan:</strong> {{ number_format($payrollHistoryDetail->total_pendapatan, 2) }}<br>
                <strong>Total Lembur:</strong> {{ number_format($payrollHistoryDetail->total_overtime, 2) }}<br>
                <strong>Total Potongan:</strong> {{ number_format($payrollHistoryDetail->total_potongan, 2) }}
            </div>
            <div class="col-md-4">
                <strong>Gaji Bruto:</strong> {{ number_format($payrollHistoryDetail->gaji_bruto, 2) }}<br>
                <strong>Gaji Bersih:</strong> {{ number_format($payrollHistoryDetail->gaji_bersih, 2) }}<br>
                <strong>ID Transaksi:</strong> {{ $payrollHistoryDetail->id_transaksi_payment ?? '-' }}
            </div>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="AttendanceDetailTable">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Earnings</th>
                <th>Deductions</th>
                <th>Earnings Overtime</th>
                <th>Overtime Hours</th>
                <th>Deduction Reason</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($attendanceDetails as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->tanggal }}</td>
                    <td>
                        <ul>
                            @foreach ($detail->earnings as $key => $value)
                                <li>{{ $key }}: {{ number_format($value, 2) }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>
                        <ul>
                            @foreach ($detail->deductions as $key => $value)
                                <li>{{ $key }}: {{ number_format($value, 2) }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>{{ number_format($detail->overtime_earnings, 2) }}</td>
                    <td>{{ $detail->overtime_hours }}</td>
                    <td>{{ $detail->deduction_reason }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#AttendanceDetailTable').DataTable({
            responsive: true,
        });
    });
</script>
