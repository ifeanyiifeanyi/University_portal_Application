@extends('admin.layouts.admin')

@section('title', 'Invoice Manager')

@section('css')
<style>
    .fas {
        font-size: 15px !important;
    }
</style>
@endsection
@php
    $statusConfig = [
        'paid' => ['bg-success', 'fas fa-check-circle', 'Paid'],
        'pending' => ['bg-warning', 'fas fa-clock', 'Pending'],
        'processing' => ['bg-info', 'fas fa-spinner', 'Processing'],
        'partial' => ['bg-primary', 'fas fa-percentage', 'Partial'],
        'rejected' => ['bg-danger', 'fas fa-times-circle', 'Rejected'],
        'failed' => ['bg-danger', 'fas fa-exclamation-circle', 'Failed'],
        'cancelled' => ['bg-secondary', 'fas fa-ban', 'Cancelled'],
        'refunded' => ['bg-info', 'fas fa-undo', 'Refunded'],
    ];
@endphp
@section('admin')
    <div class="container">
        <div class="row mb-4">
            <!-- Total Invoices Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Invoices</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $invoices->count() }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paid Invoices Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Paid Invoices</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $invoices->where('status', 'paid')->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Invoices Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pending Invoices</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $invoices->where('status', 'pending')->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Amount Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Amount</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₦{{ number_format($invoices->sum('amount'), 0, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Payment Status Chart -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Payment Status Distribution</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4">
                            <canvas id="paymentStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Payments Chart -->
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Monthly Payments</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-bar">
                            <canvas id="monthlyPaymentsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-center gap-5">
                            <div>
                                <p>
                                    <a href="{{ route('admin.payment.pay') }}" class="btn btn-primary float-end">
                                        <i class="fas fa-file-invoice fa-fw"></i> Generate New Invoice
                                    </a>
                                    &nbsp;
                                    <a href="{{ route('admin.invoice.archived') }}" class="btn btn-info float-start ml-3">
                                        <i class="fas fa-archive fa-fw"></i> View Archive
                                    </a>
                                    &nbsp;
                                    <a href="{{ route('admin.invoice.trashed') }}" class="btn btn-danger ml-3">
                                        <i class="fas fa-trash fa-fw"></i> View Trash
                                    </a>
                                    &nbsp;
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
                                        <th><i class="fas fa-building fa-fw"></i> Type</th>
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
                                            <td>
                                                @if ($invoice->is_installment)
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-percentage fa-fw"></i>
                                                        Installment
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-money-bill-wave fa-fw"></i>
                                                        Regular
                                                    </span>
                                                @endif
                                            </td>
                                            <td>₦{{ number_format($invoice->amount, 0, 2) }}</td>
                                            <td>
                                                <span class="badge {{ $statusConfig[$invoice->status][0] }}">
                                                    <i class="{{ $statusConfig[$invoice->status][1] }} fa-fw"></i>
                                                    {{ $statusConfig[$invoice->status][2] }}
                                                </span>
                                            </td>
                                            {{-- <td>
                                                <a href="{{ route('admin.invoice.show', $invoice->id) }}"
                                                    class="btn btn-info btn-sm" title="View Invoice">
                                                    <i class="fas fa-eye fa-fw"></i>
                                                </a>


                                                @if ($invoice->status == 'pending')
                                                    <button class="btn btn-danger btn-sm delete-invoice"
                                                        data-invoice-id="{{ $invoice->id }}" title="Delete Invoice">
                                                        <i class="fas fa-trash-alt fa-fw"></i>
                                                    </button>
                                                @endif

                                                @if ($invoice->status !== 'pending')
                                                    <button class="btn btn-warning btn-sm archive-invoice"
                                                        data-invoice-id="{{ $invoice->id }}" title="Archive Invoice">
                                                        <i class="fas fa-archive fa-fw"></i>
                                                    </button>
                                                @endif
                                            </td> --}}
                                            <td>
                                                <a href="{{ route('admin.invoice.show', $invoice->id) }}"
                                                    class="btn btn-info btn-sm" title="View Invoice">
                                                    <i class="fas fa-eye fa-fw text-white"></i>
                                                </a>

                                                @if ($invoice->status === 'pending')
                                                    <button class="btn btn-primary btn-sm process-payment-btn"
                                                        data-invoice-id="{{ $invoice->id }}" title="Process Payment">
                                                        <i class="fas fa-money-bill-wave fa-fw"></i>
                                                    </button>

                                                    <button class="btn btn-danger btn-sm delete-invoice"
                                                        data-invoice-id="{{ $invoice->id }}" title="Delete Invoice">
                                                        <i class="fas fa-trash-alt fa-fw"></i>
                                                    </button>
                                                @endif

                                                @if ($invoice->status === 'paid')
                                                    <button class="btn btn-warning btn-sm archive-invoice"
                                                        data-invoice-id="{{ $invoice->id }}" title="Archive Invoice">
                                                        <i class="fas fa-archive fa-fw text-white"></i>
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
                        <button type="button" class="btn btn-primary btn-sm" id="creditCardBtn">
                            <i class="fas fa-credit-card fa-fw"></i> Credit Card (Online Payment)
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" id="bankTransferBtn">
                            <i class="fas fa-university fa-fw"></i> Bank Transfer
                        </button>
                        <button type="button" class="btn btn-info btn-sm" id="cashBtn">
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const creditCardRoute = "{{ route('admin.payments.showConfirmation', ':id') }}";
        const manualPaymentRoute = "{{ route('admin.payment.pay_manual', ['invoice' => ':id']) }}";

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
                    window.location.href = creditCardRoute.replace(':id', currentInvoiceId);
                }
            });

            // Handle bank transfer
            $('#bankTransferBtn').click(function() {
                if (currentInvoiceId) {
                    window.location.href = manualPaymentRoute.replace(':id', currentInvoiceId);
                }
            });

            // Handle cash payment
            $('#cashBtn').click(function() {
                if (currentInvoiceId) {
                    window.location.href = manualPaymentRoute.replace(':id', currentInvoiceId);
                }
            });

            // Rest of your existing JavaScript code...
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

        // Handle archive invoice confirmation
        $('.archive-invoice').click(function(e) {
            e.preventDefault();
            const invoiceId = $(this).data('invoice-id');

            Swal.fire({
                title: 'Archive this invoice?',
                text: "You can still view it in the archived section",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, archive it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ url('admin/invoice/archive') }}/" + invoiceId;
                }
            });
        });
    </script>

    <script>
        // Payment Status Chart
        const statusColors = {
            'paid': '#1cc88a',
            'pending': '#f6c23e',
            'processing': '#36b9cc',
            'partial': '#4e73df',
            'rejected': '#e74a3b',
            'failed': '#e74a3b',
            'cancelled': '#858796',
            'refunded': '#36b9cc'
        };

        const statusData = @json($invoices->groupBy('status')->map(fn($group) => $group->count()));

        const ctxPie = document.getElementById('paymentStatusChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: Object.keys(statusData),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: Object.keys(statusData).map(status => statusColors[status])
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Monthly Payments Chart
        const ctxBar = document.getElementById('monthlyPaymentsChart').getContext('2d');
        const monthlyData = @json(
            $invoices->whereIn('status', ['paid', 'partial'])->groupBy(function ($invoice) {
                    return \Carbon\Carbon::parse($invoice->created_at)->format('M');
                })->map(function ($group) {
                    return $group->sum('amount');
                }));

        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: Object.keys(monthlyData),
                datasets: [{
                    label: 'Monthly Payments (₦)',
                    data: Object.values(monthlyData),
                    backgroundColor: '#36b9cc'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₦' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
