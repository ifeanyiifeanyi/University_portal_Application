@extends('admin.layouts.admin')

@section('title', 'Payment Methods')
@section('css')
    <!-- Add SweetAlert CSS if not already included in your layout -->
@endsection

@section('admin')
<div class="container">
    <div>
        <a href="{{ route('admin.payment_method.create') }}" class="btn btn-primary" id="addPaymentMethodBtn">
            <i class="fas fa-plus-circle"></i> Add Payment Method
        </a>
    </div>
    <hr />
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table">
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
                                <td>{{ Str::title($paymentMethod->name) }}</td>
                                <td>{{str_replace('_', ' ',  Str::title($paymentMethod->config['payment_type']) ) }}</td>
                                <td>
                                    <a href="{{ route('admin.payment_method.show', $paymentMethod) }}" class="btn btn-sm">
                                        <x-view-icon/>
                                    </a>
                                    <a href="{{ route('admin.payment_method.edit', $paymentMethod) }}" class="btn btn-sm">
                                        <x-edit-icon/>
                                    </a>
                                    <form action="{{ route('admin.payment_method.destroy', $paymentMethod) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm delete-btn">
                                            <x-delete-icon/>
                                        </button>
                                    </form>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add click event listener to all delete buttons
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
