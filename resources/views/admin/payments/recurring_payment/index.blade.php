@extends('admin.layouts.admin')

@section('title', 'Recurring Payment Plans')

@section('admin')
@include('admin.alert')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPlanModal">
                <i class="fas fa-plus"></i> Create New Plan
            </button>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="example" >
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Active</th>
                                <th>Subscriptions</th>
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
                                        <span class="badge bg-{{ $plan->is_active ? 'success' : 'danger' }}">
                                            {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.recurring-payments.subscriptions', $plan) }}"
                                            class="btn btn-sm btn-info">
                                            View ({{ $plan->subscriptions_count }})
                                        </a>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#editPlanModal{{ $plan->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.recurring-payments.destroy', $plan) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this plan?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No payment plans found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end">
                    {{-- {{ $plans->links() }} --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Plan Modal -->
    @include('admin.payments.recurring_payment.create-modal')

    <!-- Edit Plan Modals -->
    @foreach ($plans as $plan)
        @include('admin.payments.recurring_payment.edit-modal', ['plan' => $plan])
    @endforeach

@endsection

@section('css')
    <style>
        .modal-body .form-group {
            margin-bottom: 1rem;
        }
    </style>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createPlanForm');
            const errorDiv = document.getElementById('formErrors');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Clear previous errors
                errorDiv.classList.add('d-none');
                errorDiv.innerHTML = '';

                // Submit form via AJAX
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: form.name.value,
                        amount: form.amount.value,
                        description: form.description.value,
                        is_active: form.is_active.checked ? 1 : 0
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.errors) {
                        // Display validation errors
                        let errors = '';
                        for (const key in data.errors) {
                            errors += `<p>${data.errors[key]}</p>`;
                        }
                        errorDiv.innerHTML = errors;
                        errorDiv.classList.remove('d-none');
                    } else {
                        // Success: Reload the page or update the table dynamically
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle edit form submission
            document.querySelectorAll('[id^="editPlanForm"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const errorDiv = document.getElementById(`editFormErrors${form.dataset.planId}`);
                    errorDiv.classList.add('d-none');
                    errorDiv.innerHTML = '';

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-HTTP-Method-Override': 'PUT' // For PUT method
                        },
                        body: JSON.stringify({
                            name: form.name.value,
                            amount: form.amount.value,
                            description: form.description.value,
                            is_active: form.is_active.checked ? 1 : 0
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.errors) {
                            // Display validation errors
                            let errors = '';
                            for (const key in data.errors) {
                                errors += `<p>${data.errors[key]}</p>`;
                            }
                            errorDiv.innerHTML = errors;
                            errorDiv.classList.remove('d-none');
                        } else {
                            // Success: Reload the page or update the table dynamically
                            window.location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
    </script>
@endsection

