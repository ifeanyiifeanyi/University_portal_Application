@extends('student.layouts.student')

@section('title', 'Student Fees Manager')

@section('css')
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('student')
    <div class="container-xxl mt-3">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Student Fees Management Dashboard</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('student.view.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">School fees manager</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6"></div>
            <div class="ms-auto mb-4 col-md-6">
                <a href="{{ route('student.view.fees.pay') }}" class="btn w-100 text-white btn-success">Pay new fees</a>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Pending Payment Requirements</h5>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="pending-fees-table" class="table table-striped table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Payment Name</th>
                                        <th scope="col">Amount (₦)</th>
                                        <th scope="col">Due Date</th>
                                        <th scope="col">Late Fee Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($paymentTypes as $paymentType)
                                        <tr>
                                            <td>{{ $paymentType->paymentType->name }}</td>
                                            <td>₦{{ number_format($paymentType->paymentType->amount, 2) }}</td>
                                            <td>{{ $paymentType->paymentType->due_date->format('d.m.Y') }}</td>
                                            <td>{{ $paymentType->paymentType->late_fee_amount }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No pending payments required at this time
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            @include('messages')
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Payment Invoice History</h5>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="invoices-table" class="table table-striped table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Invoice #</th>
                                        <th scope="col">Amount (₦)</th>
                                        <th scope="col">Level</th>
                                        <th scope="col">Payment Type</th>
                                        <th scope="col">Academic Session</th>
                                        <th scope="col">Semester</th>
                                        <th scope="col">View</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($invoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->invoice_number }}</td>
                                            <td>₦{{ number_format($invoice->amount, 2) }}</td>
                                            <td>{{ $invoice->level }}</td>
                                            @if ($invoice->paymentType)
                                                <td>{{ $invoice->paymentType->name }}</td>
                                            @else
                                                <td>Not available</td>
                                            @endif
                                            <td>{{ $invoice->academicSession->name }}</td>
                                            <td>{{ $invoice->semester->name }}</td>
                                            <td><a href="{{ route('student.view.fees.invoice', ['id' => $invoice->id]) }}"
                                                    class="btn btn-sm text-white btn-success">View</a></td>
                                            <td>
                                                @if ($invoice->status == 'pending')
                                                    <form
                                                        action="{{ route('student.revoke.payment', ['id' => $invoice->id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button class="btn btn-sm text-white btn-danger"
                                                            onclick="return confirm('Are you sure you want to revoke this payment? NB: Payment can only be revoked if you haven\'t made any payment yet.')">Revoke</button>
                                                    </form>
                                                @else
                                                    <span class="badge bg-success">Paid</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No invoice records found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!--<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">-->
    <!--    <div class="modal-dialog">-->
    <!--        <div class="modal-content">-->
    <!--            <div class="modal-header">-->
    <!--                <h5 class="modal-title" id="paymentModalLabel">Payment Notification</h5>-->
    <!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
    <!--            </div>-->
    <!--            <div class="modal-body">-->
    <!--                <div id="paymentMessage"></div>-->
    <!--            </div>-->
    <!--            <div class="modal-footer">-->
    <!--                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
    <!--                <a href="{{ route('student.view.payments') }}" class="btn btn-primary" id="paymentButton">Go to Payment</a>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
    @section('javascript')
        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize DataTables
                $('#pending-fees-table').DataTable({
                    responsive: true,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search payments...",
                        emptyTable: "No pending payments available"
                    },
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'excel', 'pdf', 'print'
                    ]
                });

                $('#invoices-table').DataTable({
                    responsive: true,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search invoices...",
                        emptyTable: "No invoice records found"
                    },
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'excel', 'pdf', 'print'
                    ],
                    order: [
                        [0, 'desc']
                    ]
                });


                // Payment status check
                checkPaymentStatus();

                function checkPaymentStatus() {
                    fetch('{{ route('student.fees.checkpaymentstatus') }}')
                        .then(response => response.json())
                        .then(data => {
                            if (data.shouldShowModal && data.message) {
                                // Set the message and show modal
                                document.getElementById('paymentMessage').textContent = data.message;

                                // Set modal color based on status
                                const modalHeader = document.querySelector('.modal-header');
                                modalHeader.className = 'modal-header';
                                if (data.status === 'error') {
                                    modalHeader.classList.add('bg-danger', 'text-white');
                                } else if (data.status === 'warning') {
                                    modalHeader.classList.add('bg-warning');
                                }

                                // Initialize and show the modal
                                const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
                                paymentModal.show();

                                // Store in localStorage
                                localStorage.setItem('paymentNotificationShown', 'true');
                            }
                        })
                        .catch(error => {
                            console.error('Error checking payment status:', error);
                        });
                }
            });
        </script>
    @endsection
@endsection
