@extends('admin.layouts.admin')

@section('title', 'Subaccount Transactions')

@section('admin')
    @include('admin.alert')

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.payments.getSubaccountTransactions') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="payment_type">Payment Type</label>
                            <select name="payment_type" id="payment_type" class="form-control">
                                <option value="">Select Payment Type</option>
                                @foreach($paymentTypes as $paymentType)
                                    <option value="{{ $paymentType->id }}"
                                        {{ request('payment_type') == $paymentType->id ? 'selected' : '' }}>
                                        {{ $paymentType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
{{-- 
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="from">From Date</label>
                            <input type="date" name="from" id="from" class="form-control"
                                value="{{ request('from', now()->subDays(30)->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="to">To Date</label>
                            <input type="date" name="to" id="to" class="form-control"
                                value="{{ request('to', now()->format('Y-m-d')) }}">
                        </div>
                    </div> --}}

                    <div class="col-md-3">
                        <div class="form-group" style="margin-top: 2rem;">
                            <button type="submit" class="btn btn-primary">
                                Filter Transactions
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($selectedPaymentType)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    Transactions for {{ $selectedPaymentType->name }}
                    @if(isset($transactions['date_range']))
                        <small class="text-muted">
                            ({{ $transactions['date_range']['from'] }} to {{ $transactions['date_range']['to'] }})
                        </small>
                    @endif
                </h5>
            </div>

            @if(!empty($transactions['totals']))
                <div class="card-body border-bottom">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-muted">Total Transactions</div>
                            <div class="h4">{{ number_format($transactions['totals']['total_transactions']) }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted">Successful Transactions</div>
                            <div class="h4">{{ number_format($transactions['totals']['successful_transactions']) }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted">Total Amount</div>
                            <div class="h4">₦{{ number_format($transactions['totals']['total_amount'], 2) }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card-body">
                @if(!empty($transactions['data']))
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Channel</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions['data'] as $transaction)
                                    <tr>
                                        <td>{{ $transaction['reference'] }}</td>
                                        <td>₦{{ number_format($transaction['amount'] / 100, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $transaction['status'] === 'success' ? 'bg-success' : 'bg-danger' }}">
                                                {{ ucfirst($transaction['status']) }}
                                            </span>
                                        </td>
                                        <td>{{ $transaction['customer']['email'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($transaction['paid_at'])->format('M d, Y H:i') }}</td>
                                        <td>{{ ucfirst($transaction['channel']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted">
                            No transactions found for this period. Try expanding your date range.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
