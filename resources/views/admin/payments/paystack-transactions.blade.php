{{-- resources/views/admin/payments/paystack-transactions.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Paystack Transactions')

@section('admin')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Paystack Transactions</h3>
                    <div class="card-tools">
                        <form action="{{ route('admin.paystack.transactions') }}" method="GET" class="form-inline">
                            <div class="form-group mb-2">
                                <label class="mr-2">From:</label>
                                <input type="date" name="from" class="form-control" value="{{ $from }}">
                            </div>
                            <div class="form-group mb-2">
                                <label class="mr-2">To:</label>
                                <input type="date" name="to" class="form-control" value="{{ $to }}">
                            </div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table  table-striped">
                            <thead>
                                <tr>
                                    <th>sn</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Email</th>
                                    <th>Channel</th>
                                    <th>Status</th>
                                    <th>Gateway Response</th>
                                    <th>Paid At</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                @php
                                    $user = \App\Models\User::where('email', $transaction['customer']['email'])->first();
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $transaction['reference'] }}</td>
                                    <td>₦{{ number_format($transaction['amount'] / 100, 2) }}</td>
                                    <td>{{ $user ? $user->full_name : 'N/A' }}</td>
                                    <td>{{ $user ? $user->student->current_level : 'N/A' }}</td>
                                    <td>{{ $transaction['customer']['email'] }}</td>
                                    <td>{{ $transaction['channel'] }}</td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ ucfirst($transaction['status']) }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction['gateway_response'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction['paid_at'])->format('d F Y g:i A') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction['created_at'])->format('d F Y g:i A') }}</td>
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
$(document).ready(function() {
    $('.table').DataTable({
        "order": [[ 7, "desc" ]],
        "pageLength": 25
    });
});
</script>
@endsection
