@extends('admin.layouts.admin')
@section('title', 'Payment Type Details')
@section('admin')
    <div class="container">
        @include('admin.alert')

        <div class="card">

            <div class="card-header">
                <div class="row">
                    <div class="d-flex justify-content-end">

                        <a href="{{ route('admin.payment_type.edit', $paymentType) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        &nbsp;
                        <form action="{{ route('admin.payment_type.destroy', $paymentType) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this payment type?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
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
                    <dt class="col-sm-3">Description:</dt>
                    <dd class="col-sm-9">{{ $paymentType->description }}</dd>
                    <dt class="col-sm-3">Departments:</dt>
                    <dd class="col-sm-9">
                        @foreach ($paymentType->departments as $department)
                            {{ $department->name }} (Level: {{ $department->pivot->level }})<br>
                        @endforeach
                    </dd>
                </dl>

            </div>
        </div>
    </div>
@endsection
