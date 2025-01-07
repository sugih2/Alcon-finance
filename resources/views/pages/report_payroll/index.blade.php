@extends('../layouts.app')

@section('content')
    @include('../layouts.navbars.auth.topnav', ['title' => 'Report Payroll'])
    @include('sweetalert::alert')
    <div class="row mt-4 mx-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Report Payroll</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="container">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="ReportpayrollTable">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            No
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            No Transaction
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Periode</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Amount</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Total Karyawan</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Status</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Description</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index = 1; @endphp
                                    @foreach ($datatransaksis as $e)
                                        <tr>
                                            <td class="align-middle text-center">{{ $index++ }}</td>
                                            <td>{{ $e->id_transaksi_payment }}</td>
                                            <td>{{ \Carbon\Carbon::parse($e->start_periode)->format('d F') }} -
                                                {{ \Carbon\Carbon::parse($e->end_periode)->format('d F Y') }}</td>
                                            <td>Rp {{ number_format($e->amount_transaksi, 0, ',', '.') }}</td>
                                            <td>{{ $e->total_karyawan }}</td>
                                            <td>{{ $e->status_payroll }}</td>
                                            <td>{{ $e->description }}</td>

                                            <td class="align-middle text-end">
                                                <button type="button" class="btn btn-link text-info mb-0"
                                                onclick="window.location.href='{{ route('historypayrollGroup.index', ['id' => $e->id]) }}'">
                                                Detail Group
                                            </button>
                                                {{-- <button type="button" class="btn btn-primary"
                                                    onclick="payment({{ $e->id }})"> Proces Payment
                                                </button> --}}
                                                @if($e->status_payroll === 'pending')
                                                <button type="button" class="btn btn-primary"
                                                    onclick="payment({{ $e->id }})">Process Payment</button>
                                                @else
                                                <button type="button" class="btn btn-secondary" disabled>Sudah Payment</button>
                                                @endif
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
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel">Tambah Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="createEmployee"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Payment -->
    <div class="modal fade" id="PaymentModal" tabindex="-1" aria-labelledby="PaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="PaymentModalLabel">Payment Provess</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="CloseBtnPay"></button>
                </div>
                <div class="modal-body">
                    <div id="payment"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="DetailPayrollModal" tabindex="-1" aria-labelledby="DetailPayrollModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="DetailPayrollModalLabel">Payroll History Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="CloseDetail"></button>
                </div>
                <div class="modal-body">
                    <div id="detailPayroll"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#ReportpayrollTable').DataTable({
                responsive: true,
            });
        });

        function payment(id) {
            $.ajax({
                url: "{{ url('/payment') }}/" + id,
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    $("#payment").html(data);
                    $('#PaymentModal').modal('show');
                    $('#payment-amount').on('input', function() {
                        var value = $(this).val();

                        value = value.replace(/[^\d]/g, '');
                        var numericValue = parseFloat(value);

                        var rupiah = '';
                        var valueLength = value.length;

                        for (var i = valueLength; i > 0; i--) {
                            if ((valueLength - i) % 3 === 0 && i !== valueLength) {
                                rupiah = '.' + rupiah;
                            }
                            rupiah = value[i - 1] + rupiah;
                        }

                        $(this).val(rupiah);
                        $(this).data('numericValue', numericValue);
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

        function PaymentProcess(id) {
            event.preventDefault();

            var form = $('#FormPayment')[0];
            var formData = new FormData(form);

            if (!formData.has('payment_amount')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Payment Amount is required.',
                });
                return;
            }

            var rawValue = formData.get('payment_amount') || '';
            rawValue = rawValue.replace(/\./g, '');
            formData.set('payment_amount', rawValue);


            // formData.forEach(function(value, key) {
            //     console.log(`${key}: ${value}`);
            // });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ url('/payment/process') }}/" + id,
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function(data, textStatus, xhr) {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: data.message,
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#CloseBtnPay').trigger('click');
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: data.message || 'Unexpected response from the server.',
                        });
                    }
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
                    } else if (xhr.status === 403) {
                        var response = xhr.responseJSON;

                        Swal.fire({
                            icon: 'error',
                            title: 'Access Denied',
                            text: response.message ||
                                'You do not have permission to perform this action.',
                        });
                    } else if (xhr.status === 409) {
                        var response = xhr.responseJSON;

                        Swal.fire({
                            icon: 'warning',
                            title: 'Conflict Detected',
                            text: response.message || 'Conflict occurred during the operation.',
                        });
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
    </script>
@endsection
