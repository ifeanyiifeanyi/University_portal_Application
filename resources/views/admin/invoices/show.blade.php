{{-- resources/views/admin/invoices/show.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Invoice Detail')

@section('css')
    <!-- Add SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.1/sweetalert2.min.css">
@endsection

@section('admin')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="fas fa-file-invoice fa-fw"></i> Invoice Details
                        </h4>
                        <div class="float-end">
                            <a href="" class="btn btn-secondary" title="Download Invoice">
                                <i class="fas fa-file-pdf fa-fw"></i> Download PDF
                            </a>
                            @if($invoice->status === 'pending')
                                <button class="btn btn-success mark-paid" title="Mark as Paid"
                                    data-invoice-id="{{ $invoice->id }}">
                                    <i class="fas fa-check-circle fa-fw"></i> Mark as Paid
                                </button>
                                <button class="btn btn-danger cancel-invoice" title="Cancel Invoice"
                                    data-invoice-id="{{ $invoice->id }}">
                                    <i class="fas fa-ban fa-fw"></i> Cancel Invoice
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-info-circle fa-fw"></i> Invoice Information</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th><i class="fas fa-hashtag fa-fw"></i> Invoice Number</th>
                                        <td>{{ $invoice->invoice_number }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-chart-pie fa-fw"></i> Status</th>
                                        <td>
                                            @if($invoice->status === 'paid')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle fa-fw"></i> Paid
                                                </span>
                                            @elseif($invoice->status === 'pending')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock fa-fw"></i> Pending
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle fa-fw"></i> Cancelled
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-money-bill-wave fa-fw"></i> Amount</th>
                                        <td>₦{{ number_format($invoice->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-credit-card fa-fw"></i> Payment Type</th>
                                        <td>{{ $invoice->paymentType->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-money-check-alt fa-fw"></i> Payment Method</th>
                                        <td>{{ $invoice->paymentMethod->name }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-user-graduate fa-fw"></i> Student Information</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th><i class="fas fa-user fa-fw"></i> Student Name</th>
                                        <td>{{ $invoice->student->user->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-building fa-fw"></i> Department</th>
                                        <td>{{ $invoice->department->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-layer-group fa-fw"></i> Level</th>
                                        <td>{{ $invoice->level }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-calendar-alt fa-fw"></i> Academic Session</th>
                                        <td>{{ $invoice->academicSession->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-clock fa-fw"></i> Semester</th>
                                        <td>{{ $invoice->semester->name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($invoice->payment)
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5><i class="fas fa-money-check fa-fw"></i> Payment Information</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th><i class="fas fa-calendar-check fa-fw"></i> Payment Date</th>
                                            <td>{{ $invoice->payment->created_at->format('d M, Y H:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-fingerprint fa-fw"></i> Transaction Reference</th>
                                            <td>{{ $invoice->payment->transaction_reference }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.1/sweetalert2.all.min.js"></script>

    <script>
        // Show success message if exists in session
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Show error message if exists in session
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: "{{ session('error') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Handle Mark as Paid confirmation
        $('.mark-paid').click(function(e) {
            e.preventDefault();
            const invoiceId = $(this).data('invoice-id');

            Swal.fire({
                title: 'Mark Invoice as Paid?',
                text: "Are you sure you want to mark this invoice as paid?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, mark as paid!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ url('admin/invoice/mark-paid') }}/" + invoiceId;
                }
            });
        });

        // Handle Cancel Invoice confirmation
        $('.cancel-invoice').click(function(e) {
            e.preventDefault();
            const invoiceId = $(this).data('invoice-id');

            Swal.fire({
                title: 'Cancel Invoice?',
                text: "Are you sure you want to cancel this invoice? This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, cancel it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ url('admin/invoice/cancel') }}/" + invoiceId;
                }
            });
        });
    </script>
@endsection
