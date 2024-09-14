@extends('admin.layouts.admin')

@section('title', 'Payment Type Details')

@section('admin')
    <div class="container">
        @include('admin.alert')

        <div class="card p-3">
            <div class="row">
                @include('admin.return_btn')

                <div class="col-md-8 mx-auto">
                    <h4>Payment Type Details</h4>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Name:</dt>
                            <dd class="col-sm-9">{{ $paymentType->name }}</dd>

                            <dt class="col-sm-3">Amount:</dt>
                            <dd class="col-sm-9">₦{{ number_format($paymentType->amount, 2) }}</dd>



                            <dt class="col-sm-3">Status:</dt>
                            <dd class="col-sm-9">{{ $paymentType->is_active ? 'Active' : 'Inactive' }}</dd>

                            <dt class="col-sm-3">Academic Session:</dt>
                            <dd class="col-sm-9">{{ $paymentType->academicSession->name }}</dd>

                            <dt class="col-sm-3">Semester:</dt>
                            <dd class="col-sm-9">{{ $paymentType->semester->name }}</dd>
                            <dt class="col-sm-12 mx-3 my-3">
                                <dt> Description:</dt>
                                <dd>{{ $paymentType->description }}</dd>
                            </dt>

                            <dt class="col-sm-3">Departments:</dt>
                            <dd class="col-sm-9">
                                @foreach ($paymentType->departments as $department)
                                    {{ $department->name }} (Level: {{ $department->pivot->level }})<br>
                                @endforeach
                            </dd>
                        </dl>

                        <div class="mt-3">
                            <a href="{{ route('admin.payment_type.edit', $paymentType) }}" class="btn btn-primary">Edit</a>
                            <form action="{{ route('admin.payment_type.destroy', $paymentType) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this payment type?')">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
