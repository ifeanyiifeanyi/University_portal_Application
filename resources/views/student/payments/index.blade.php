@extends('student.layouts.student')

@section('title', 'Payment History & Transactions')

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
                <h4 class="fs-18 fw-semibold m-0">Payment History & Transactions</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('student.view.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Payment History</li>
                </ol>
            </div>
        </div>

        <div class="row">
            @include('messages')
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Transaction Records</h5>

                        <form method="get" class="d-flex align-items-center">
                            <select name="status" class="form-control mr-2" onchange="this.form.submit()">
                                <option value="all">All Payments</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}"
                                        {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="payments-history-table" class="table table-striped table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Payment Type</th>
                                        <th scope="col">Session/Semester</th>
                                        <th scope="col">Original Amount</th>
                                        <th scope="col">Paid Amount</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($payments as $payment)
                                        <tr>
                                            <td>
                                                {{ $payment->paymentType->name }}
                                                @if ($payment->is_installment)
                                                    <span class="badge bg-info">Installment</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $payment->academicSession->name }} / {{ $payment->semester->name }}
                                            </td>
                                            <td>₦{{ number_format($payment->amount, 2) }}</td>
                                            <td>₦{{ number_format($payment->base_amount, 2) }}</td>
                                            <td>
                                                <span
                                                    class="badge
                                            @if ($payment->status === 'paid') bg-success
                                            @elseif($payment->status === 'partial') bg-warning
                                            @else bg-danger @endif">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                            <td>
                                                <div class="d-flex flex-column gap-2">
                                                    @if ($payment->is_installment && $payment->status === 'partial')
                                                        <a href="{{ route('student.fees.payments.installment', $payment->id) }}"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="fas fa-file-invoice-dollar me-1"></i>
                                                            Pay Installment
                                                            (₦{{ number_format($payment->next_transaction_amount, 2) }})
                                                        </a>
                                                    @endif

                                                    @if ($payment->status === 'pending')
                                                        <a href="{{ route('student.view.fees.invoice', ['id' => $payment?->invoice?->id]) }}"
                                                            class="btn btn-sm btn-success">
                                                            <i class="fas fa-credit-card me-1"></i> Continue Payment
                                                        </a>
                                                    @endif

                                                    @if ($payment->status === 'paid' || $payment->status === 'partial')
                                                        @if ($payment->receipt)
                                                            <a href="{{ route('student.fees.payments.showReceipt', ['receipt' => $payment->receipt->id]) }}"
                                                                class="btn btn-sm btn-info">
                                                                <i class="fas fa-file-invoice me-1"></i> View Receipt
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-center">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

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
        $(document).ready(function() {
            // Initialize DataTable with pagination handled by Laravel
            $('#payments-history-table').DataTable({
                responsive: true,
                paging: false, // Disable DataTables pagination since we're using Laravel's
                searching: true,
                ordering: true,
                info: false, // Hide "Showing X of Y entries"
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search transactions...",
                    emptyTable: "No payment records found"
                },
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf', 'print'
                ],
                order: [
                    [5, 'desc']
                ] // Sort by date column descending
            });
        });
    </script>
@endsection
@endsection
