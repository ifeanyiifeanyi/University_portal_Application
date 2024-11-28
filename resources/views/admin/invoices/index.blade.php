@extends('admin.layouts.admin')

@section('title', 'Invoice Manager')

@section('css')
@endsection

@section('admin')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-center gap-5">
                            <div>
                                <p>
                                    <a href="{{ route('admin.payment.pay') }}" class="btn btn-primary float-left">
                                        <i class="fas fa-file-invoice fa-fw"></i> Generate New Invoice
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="example">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-hashtag fa-fw"></i> SN</th>
                                        <th><i class="fas fa-file-invoice fa-fw"></i> Invoice ID</th>
                                        <th><i class="fas fa-user-graduate fa-fw"></i> Student Name</th>
                                        <th><i class="fas fa-building fa-fw"></i> Department</th>
                                        <th><i class="fas fa-money-bill-wave fa-fw"></i> Amount</th>
                                        <th><i class="fas fa-info-circle fa-fw"></i> Status</th>
                                        <th><i class="fas fa-cogs fa-fw"></i> Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoices as $invoice)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $invoice->invoice_number }}</td>
                                            <td>{{ $invoice->student->user->full_name }}</td>
                                            <td>{{ $invoice->department->name }}</td>
                                            <td>₦{{ number_format($invoice->amount, 0, 2) }}</td>
                                            <td>
                                                @if ($invoice->status == 'paid')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle fa-fw"></i> Paid
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock fa-fw"></i> Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.invoice.show', $invoice->id) }}"
                                                    class="btn btn-info btn-sm" title="View Invoice">
                                                    <i class="fas fa-eye fa-fw"></i>
                                                </a>

                                                @if ($invoice->status == 'paid')
                                                    <a href="" class="btn btn-success btn-sm" title="Edit Invoice">
                                                        <i class="fas fa-edit fa-fw"></i>
                                                    </a>
                                                @endif

                                                @if ($invoice->status == 'pending')
                                                    <a href="#" class="btn btn-primary btn-sm process-payment-btn"
                                                        data-invoice-id="{{ $invoice->id }}" title="Process Payment">
                                                        <i class="fas fa-credit-card fa-fw"></i>
                                                    </a>

                                                    <button class="btn btn-danger btn-sm delete-invoice"
                                                        data-invoice-id="{{ $invoice->id }}" title="Delete Invoice">
                                                        <i class="fas fa-trash-alt fa-fw"></i>
                                                    </button>
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
    <div class="modal fade" id="paymentMethodModal" tabindex="-1" aria-labelledby="paymentMethodModalLabel"
        aria-hidden="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentMethodModalLabel">
                        <i class="fas fa-money-bill fa-fw"></i> Select Payment Method
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-3">
                        <button type="button" class="btn btn-primary btn-lg" id="creditCardBtn">
                            <i class="fas fa-credit-card fa-fw"></i> Credit Card (Online Payment)
                        </button>
                        <button type="button" class="btn btn-secondary btn-lg" id="bankTransferBtn">
                            <i class="fas fa-university fa-fw"></i> Bank Transfer
                        </button>
                        <button type="button" class="btn btn-info btn-lg" id="cashBtn">
                            <i class="fas fa-money-bill-wave fa-fw"></i> Cash Payment
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="fas fa-times fa-fw"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        // Add this to your JavaScript section
        $(document).ready(function() {
            // Store the current invoice ID
            let currentInvoiceId = null;

            // When the process payment button is clicked
            $('.process-payment-btn').click(function(e) {
                e.preventDefault();
                currentInvoiceId = $(this).data('invoice-id');
                $('#paymentMethodModal').modal('show');
            });

            // Handle credit card payment
            $('#creditCardBtn').click(function() {
                if (currentInvoiceId) {
                    window.location.href = `{{ route('admin.payments.showConfirmation', $invoice->id) }}`;
                }
            });

            // Handle bank transfer
            $('#bankTransferBtn').click(function() {
                if (currentInvoiceId) {
                    window.location.href =
                        `{{ route('admin.payment.pay_manual', ['invoice' => $invoice->id]) }}`;
                }
            });

            // Handle cash payment
            $('#cashBtn').click(function() {
                if (currentInvoiceId) {
                    window.location.href =
                        `{{ route('admin.payment.pay_manual', ['invoice' => $invoice->id]) }}`;
                }
            });
        });




        // Show success message if exists in session
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Show error message if exists in session
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: "{{ session('error') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Handle delete invoice confirmation
        $('.delete-invoice').click(function(e) {
            e.preventDefault();
            const invoiceId = $(this).data('invoice-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ url('admin/invoice/cancel') }}/" + invoiceId;
                }
            });
        });
    </script>
@endsection
