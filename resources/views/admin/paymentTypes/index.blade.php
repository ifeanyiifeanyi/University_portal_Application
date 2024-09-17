@extends('admin.layouts.admin')

@section('title', 'Payment Type Manager')
@section('css')

@endsection

@section('admin')

    <div class="container">
        <div>
            <a href="{{ route('admin.payment_type.create') }}" class="btn btn-primary" id="addPaymentTypeBtn">Add Payment Type</a>
        </div>
        <hr />
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>sn</th>
                                <th>Payment Type</th>
                                <th>Amount</th>
                                <th>Department</th>
                                <th>Semester</th>
                                <th>Academic Session</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paymentTypes as $key => $paymentType)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $paymentType->name }}</td>
                                    <td>₦{{ number_format($paymentType->amount, 2) }}</td>
                                    <td>{{ $paymentType->departments->first()->name }}</td>
                                    <td>{{ $paymentType->semester->name }}</td>
                                    <td>{{ $paymentType->academicSession->name }}</td>
                                    <td>
                                        <a href="{{ route('admin.payment_type.show', $paymentType->id) }}"><i class="bx bx-info-circle"></i></a>
                                        <a href="{{ route('admin.payment_type.edit', $paymentType->id) }}"><i class="bx bx-pencil"></i></a>
                                        <a onclick="return confirm('Are you sure of this action ?')" href="{{ route('admin.payment_type.destroy', $paymentType->id) }}"><i class="bx bx-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')

@endsection
