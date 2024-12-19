@extends('admin.layouts.admin')

@section('title', 'Archived Invoices')

@section('admin')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-archive fa-fw"></i> Archived Invoices
                    </h4>
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
                                    <th><i class="fas fa-calendar fa-fw"></i> Archived Date</th>
                                    <th><i class="fas fa-cogs fa-fw"></i> Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($archivedInvoices as $invoice)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $invoice->invoice_number }} <br>
                                            <a href="" class="btn-sm btn btn-primary">Retrieve</a>
                                        </td>
                                        <td>{{ $invoice->student->user->full_name }}</td>
                                        <td>{{ $invoice->department->name }}</td>
                                        <td>₦{{ number_format($invoice->amount, 0, 2) }}</td>
                                        <td>
                                            @if ($invoice->status == 'paid')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle fa-fw"></i> Paid
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $invoice->archived_at->format('d M, Y H:i A') }}</td>
                                        <td>
                                            <a href="{{ route('admin.invoice.show', $invoice->id) }}"
                                                class="btn btn-info btn-sm" title="View Invoice">
                                                <i class="fas fa-eye fa-fw"></i>
                                            </a>
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
