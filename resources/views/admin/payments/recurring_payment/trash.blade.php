@extends('admin.layouts.admin')

@section('title', 'Recurring Payment Plans')

@section('admin')
@include('admin.alert')
    <div class="container-fluid">


        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="example" >
                        <caption>Deleted Recurring Payment Plans</caption>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plans as $plan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $plan->name }}</td>
                                    <td>₦{{ number_format($plan->amount, 2) }}</td>

                                    <td>
                                        <a href="{{ route('admin.recurring-payments.restore', $plan) }}" class="btn btn-sm btn-primary" title="Restore Plan">
                                            <i class="fas fa-recycle"></i>
                                        </a>
                                        <a class="btn btn-sm btn-danger" href="{{ route('admin.recurring-payments.force-destroy', $plan) }}" onclick="return confirm('This action is irreversible. Are you sure you want to delete this plan?')">
                                            <i class='bx bxs-trash'></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No trashed payment plans found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>


@endsection

@section('css')
    <style>
        .modal-body .form-group {
            margin-bottom: 1rem;
        }
    </style>
@endsection

@section('javascript')

@endsection

