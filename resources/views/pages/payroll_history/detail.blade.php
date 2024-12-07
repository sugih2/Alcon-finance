<form action="">
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Payroll History Detail</h6>
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
                                    <td>Rp. {{ number_format($payrollHistoryDetail->amount_transaksi, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>
                            <table class="table align-items-center mb-0" id="PayrollHistoryDetailTable">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            No
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            No Transaction
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Name</th>
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
                                    @foreach ($payrollHistoryDetail->detailPayroll as $index => $detail)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $detail->id_transaksi_payment ?? '-' }}</td>
                                            <td>{{ $detail->employee->name ?? '-' }}</td>
                                            <td>Rp. {{ number_format($detail->salary, 0, ',', '.') }}</td>
                                            <td>
                                                @if (!empty($detail->allowance))
                                                    <ul>
                                                        @foreach ($detail->allowance as $allowance)
                                                            <li>{{ $allowance['nama'] }}:
                                                                {{ number_format($allowance['nilai'], 0, ',', '.') }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if (!empty($detail->deduction))
                                                    <ul>
                                                        @foreach ($detail->deduction as $deduction)
                                                            <li>{{ $deduction['nama'] }}:
                                                                Rp.
                                                                {{ number_format($deduction['nilai'], 0, ',', '.') }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>Rp. {{ number_format($detail->total_pendapatan, 0, ',', '.') }}</td>
                                            <td>Rp. {{ number_format($detail->total_potongan, 0, ',', '.') }}</td>
                                            <td>Rp. {{ number_format($detail->gaji_bruto, 0, ',', '.') }}</td>
                                            <td>Rp. {{ number_format($detail->gaji_bersih, 0, ',', '.') }}</td>
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

    {{-- <script>
        $(document).ready(function() {
            $('#PayrollHistoryDetailTable').DataTable({
                responsive: true,
            });
        });
    </script> --}}
</form>
