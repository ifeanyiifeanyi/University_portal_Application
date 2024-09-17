@extends('admin.layouts.admin')

@section('title', 'Payment Method Details')

@section('admin')
<div class="container">
    <div>
        @include('admin.return_btn')
    </div>
    @include('admin.alert')
    <hr />
    <div class="row">
        <div class="col-md-7 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">{{ Str::title($paymentMethod->name) }}</h2>
                    <p><strong>Description:</strong> <br>{{ $paymentMethod->description }}</p>
                    <p><strong>Status:</strong> {{ $paymentMethod->is_active ? 'Active' : 'Inactive' }}</p>
                    <p><strong>Payment Type:</strong> {{str_replace('_', ' ', Str::title($paymentMethod->config['payment_type'])) ?? 'N/A' }}</p>
                    
                    @if($paymentMethod->config['payment_type'] == 'credit_card')
                        <p><strong>Gateway:</strong> {{ $paymentMethod->config['gateway'] ?? 'N/A' }}</p>
                    @endif

                    @if($paymentMethod->logo)
                        <img src="{{ asset('storage/' . $paymentMethod->logo) }}" alt="{{ $paymentMethod->name }} Logo" class="img-fluid mb-3" style="max-width: 100px;border-radius:50%">
                    @endif

                    <h3>Configuration:</h3>
                    <ul>
                        @foreach($paymentMethod->config as $key => $value)
                            @if($key != 'payment_type' && $key != 'gateway')
                                <li><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</li>
                            @endif
                        @endforeach
                    </ul>

                    <a href="{{ route('admin.payment_method.edit', $paymentMethod) }}" class="btn btn-primary">Edit</a>
                    <form action="{{ route('admin.payment_method.destroy', $paymentMethod) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Are you sure of this action ?')" type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this payment method?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection