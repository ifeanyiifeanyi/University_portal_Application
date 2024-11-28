@extends('admin.layouts.admin')

@section('title', 'Payment Type Manager')
@section('css')
    <style>
        .action-icons a {
            padding: 5px;
            color: inherit;
            text-decoration: none;
        }

        .action-icons a:hover {
            opacity: 0.8;
        }

        .modal-dialog {
            max-width: 700px;
        }
    </style>
@endsection

@section('admin')
    <a href="{{ route('admin.payment_type.create') }}" class="btn btn-primary" id="addPaymentTypeBtn">
        <i class="fas fa-plus me-2"></i>Add Payment
    </a>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag me-2"></i>SN</th>
                            <th><i class="fas fa-money-bill-wave me-2"></i>Amount</th>
                            <th><i class="fas fa-building me-2"></i>Department</th>
                            <th><i class="fas fa-calendar-alt me-2"></i>Semester</th>
                            <th><i class="fas fa-graduation-cap me-2"></i>Academic Session</th>
                            <th><i class="fas fa-cogs me-2"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paymentTypes as $key => $paymentType)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>₦{{ number_format($paymentType->amount, 2) }}</td>
                                <td>{{ $paymentType->departments->first()->name }}</td>
                                <td>{{ $paymentType->semester->name }}</td>
                                <td>{{ $paymentType->academicSession->name }}</td>
                                <td class="action-icons">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#viewModal"
                                        data-id="{{ $paymentType->id }}" data-name="{{ $paymentType->name }}"
                                        data-amount="{{ number_format($paymentType->amount, 2) }}"
                                        data-status="{{ $paymentType->is_active ? 'Active' : 'Inactive' }}"
                                        data-academic-session="{{ $paymentType->academicSession->name }}"
                                        data-semester="{{ $paymentType->semester->name }}"
                                        data-description="{{ $paymentType->description }}"
                                        data-departments="@foreach ($paymentType->departments as $department){{ $department->name }} (Level: {{ $department->pivot->level }})@if (!$loop->last)&#13;@endif @endforeach"
                                        data-edit-url="{{ route('admin.payment_type.edit', $paymentType->id) }}"
                                        class="text-info view-details" title="View Details">
                                        <x-view-icon />
                                    </a>
                                    <a href="{{ route('admin.payment_type.edit', $paymentType->id) }}" title="Edit"
                                        class="text-primary">
                                        <x-edit-icon />
                                    </a>
                                    <a href="#" class="text-danger delete-btn" data-id="{{ $paymentType->id }}"
                                        title="Delete">
                                        <x-delete-icon />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">Payment Type Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-end mb-3">
                        <a href="#" id="modalEditBtn" class="btn btn-secondary btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" id="modalDeleteBtn" class="btn btn-danger btn-sm ms-2">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                    <dl class="row">
                        <dt class="col-sm-3">Name:</dt>
                        <dd class="col-sm-9" id="modal-name"></dd>

                        <dt class="col-sm-3">Amount:</dt>
                        <dd class="col-sm-9" id="modal-amount"></dd>

                        <dt class="col-sm-3">Status:</dt>
                        <dd class="col-sm-9" id="modal-status"></dd>

                        <dt class="col-sm-3">Academic Session:</dt>
                        <dd class="col-sm-9" id="modal-academic-session"></dd>

                        <dt class="col-sm-3">Semester:</dt>
                        <dd class="col-sm-9" id="modal-semester"></dd>

                        <dt class="col-sm-3">Description:</dt>
                        <dd class="col-sm-9" id="modal-description"></dd>

                        <dt class="col-sm-3">Departments:</dt>
                        <dd class="col-sm-9" id="modal-departments"></dd>
                    </dl>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle delete button clicks
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    confirmDelete(id);
                });
            });

            // Handle modal data population
            const viewModal = document.getElementById('viewModal');
            viewModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const data = button.dataset;

                // Populate modal content
                document.getElementById('modal-name').textContent = data.name;
                document.getElementById('modal-amount').textContent = '₦' + data.amount;
                document.getElementById('modal-status').textContent = data.status;
                document.getElementById('modal-academic-session').textContent = data.academicSession;
                document.getElementById('modal-semester').textContent = data.semester;
                document.getElementById('modal-description').textContent = data.description;
                document.getElementById('modal-departments').innerHTML = data.departments.replace(/&#13;/g,
                    '<br>');

                // Set up action buttons
                const modalEditBtn = document.getElementById('modalEditBtn');
                modalEditBtn.href = data.editUrl;

                const modalDeleteBtn = document.getElementById('modalDeleteBtn');
                modalDeleteBtn.dataset.id = data.id;
            });

            // Modal delete button
            document.getElementById('modalDeleteBtn').addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.dataset.id;
                confirmDelete(id);
            });

            function confirmDelete(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This payment type will be deleted permanently!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('admin.payment_type.destroy', '') }}/" + id;
                    }
                });
            }
        });
    </script>
@endsection
