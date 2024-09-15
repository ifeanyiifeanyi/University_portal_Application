@extends('admin.layouts.admin')

@section('title', 'Payment Methods')
@section('css')

@endsection



@section('admin')
<div class="container">
    <div>
        <a href="{{ route('admin.payment_method.create') }}" class="btn btn-primary" id="addPaymentMethodBtn"><i class="fadeIn animated bx bx-add-to-queue"></i> Add
            Payment Method</a>
    </div>
    <hr />
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>sn</th>
                            <th>Name</th>
                            <th>Payment Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentMethods as $key => $paymentMethod)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $paymentMethod->name }}</td>

                                <td>{{ $paymentMethod->payment_type }}</td>
                                <td>
                                    <a href="" class="btn btn-sm btn-info">View</a>
                                    <a href="" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="" class="btn btn-sm btn-danger">Delete</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                </table>
        </div>
    </div>
</div>
@endsection

@section('javascript')

@endsection
