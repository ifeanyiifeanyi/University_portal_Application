@extends('admin.layouts.admin')

@section('title', 'Installment Configuration')


@section('admin')
    @include('admin.alert')
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Installment Configurations</h5>
            </div>
            <div class="card-body">
                <!-- Add New Configuration Form -->
                <form action="{{ route('admin.installment-config.store') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Payment Type</label>
                            <select name="payment_type_id" class="form-select">
                                @foreach ($paymentTypes as $type)
                                    <option {{ old('payment_type_id') == $type->id ? 'selected' : '' }}
                                        value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('payment_type_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Number of Installments</label>
                            <input type="number" name="number_of_installments" min="2"
                                value="{{ old('number_of_installments', 2) }}" class="form-control">
                            @error('number_of_installments')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Minimum First Payment (%)</label>
                            <input type="number" name="minimum_first_payment_percentage" min="1" max="99"
                                value="60" class="form-control">
                            @error('minimum_first_payment_percentage')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Interval Days</label>
                            <input type="number" name="interval_days" min="1" value="{{ old('interval_days', 30) }}"
                                class="form-control">
                            @error('interval_days')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Create Configuration
                    </button>
                </form>

                <!-- Existing Configurations -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Payment Type</th>
                                <th>Installments</th>
                                <th>First Payment %</th>
                                <th>Interval Days</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($configs as $config)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $config->paymentType->name }}</td>
                                    <td>{{ $config->number_of_installments }}</td>
                                    <td>{{ $config->minimum_first_payment_percentage }}%</td>
                                    <td>{{ $config->interval_days }} days</td>
                                    <td>
                                        <span class="badge bg-{{ $config->is_active ? 'success' : 'danger' }}">
                                            {{ $config->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editConfig({{ $config->id }})">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.installment-config.destroy', $config->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this configuration?')">
                                                Delete
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editConfigModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editConfigForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Number of Installments</label>
                            <input type="number" name="number_of_installments" min="2" class="form-control">
                            @error('number_of_installments')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Minimum First Payment (%)</label>
                            <input type="number" name="minimum_first_payment_percentage" min="1" max="99"
                                class="form-control">
                            @error('minimum_first_payment_percentage')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Interval Days</label>
                            <input type="number" name="interval_days" min="1" class="form-control">
                            @error('interval_days')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="mb-3 form-check">
                            <input type="checkbox" value="1" name="is_active" class="form-check-input" id="isActive">
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Configuration</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection



@section('css')

@endsection
@section('javascript')
    <script>
        function editConfig(configId) {
            // Fetch configuration data
            fetch(`/admin/installment-config/${configId}/edit`)
                .then(response => response.json())
                .then(data => {
                    const form = document.getElementById('editConfigForm');

                    // Update form action URL
                    form.action = `/admin/installment-config/${configId}/update`;

                    // Set form values
                    form.querySelector('[name="number_of_installments"]').value = data.number_of_installments;
                    form.querySelector('[name="minimum_first_payment_percentage"]').value = data
                        .minimum_first_payment_percentage;
                    form.querySelector('[name="interval_days"]').value = data.interval_days;
                    form.querySelector('[name="is_active"]').checked = data.is_active;

                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('editConfigModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching configuration data');
                });
        }
    </script>
@endsection
