@extends('admin.layouts.admin')

@section('title', 'Trashed Invoices')

@section('admin')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center">
                            <h3 class="text-primary">Trashed Invoices</h3>
                            <a href="{{ route('admin.invoice.view') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left fa-fw"></i> Back to Invoices
                            </a>
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
                                        <th><i class="fas fa-calendar fa-fw"></i> Deleted At</th>
                                        <th><i class="fas fa-cogs fa-fw"></i> Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($trashedInvoices as $invoice)
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
                                            <td>{{ $invoice->deleted_at->format('d M Y, h:i A') }}</td>
                                            <td>
                                                <a href="{{ route('admin.invoice.restore', $invoice->id) }}"
                                                    class="btn btn-success btn-sm restore-invoice" title="Restore Invoice">
                                                    <i class="fas fa-trash-restore fa-fw"></i>
                                                </a>

                                                <button class="btn btn-danger btn-sm force-delete-invoice"
                                                    data-invoice-id="{{ $invoice->id }}" title="Permanently Delete">
                                                    <i class="fas fa-trash-alt fa-fw"></i>
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
@endsection

@section('javascript')
    <script>
        // Handle force delete confirmation
        $('.force-delete-invoice').click(function(e) {
            e.preventDefault();
            const invoiceId = $(this).data('invoice-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the invoice. This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete permanently!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/invoice/force-delete') }}/" + invoiceId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            location.reload();
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Something went wrong!', 'error');
                        }
                    });
                }
            });
        });

        // Handle restore confirmation
        $('.restore-invoice').click(function(e) {
            e.preventDefault();
            const restoreUrl = $(this).attr('href');

            Swal.fire({
                title: 'Restore Invoice?',
                text: "This will restore the invoice and make it active again",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, restore it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = restoreUrl;
                }
            });
        });
    </script>
@endsection
