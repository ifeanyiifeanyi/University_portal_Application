@extends('admin.layouts.admin')

@section('title', 'Archived Invoices')
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
                                            <a href="{{ route('admin.invoice.reverseArchive', $invoice) }}" class="btn-sm btn btn-primary"> <i class="fas fa-recycle"></i> Retrieve</a>
                                        </td>
                                        <td>{{ $invoice->student->user->full_name }}</td>
                                        <td>{{ $invoice->department->name }}</td>
                                        <td>₦{{ number_format($invoice->amount, 0, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $statusConfig[$invoice->status][0] }}">
                                                <i class="{{ $statusConfig[$invoice->status][1] }} fa-fw"></i>
                                                {{ $statusConfig[$invoice->status][2] }}
                                            </span>
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
