@extends('admin.layouts.admin')

@section('title', 'Dashboard')
@section('css')

@endsection



@section('admin')
    @include('admin.alert')

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Submit Payment Proof</h4>
            </div>
            <div class="card-body">
                <!-- Invoice Details Summary -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="border-bottom pb-2">Invoice Details</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <p class="text-muted mb-1">Invoice Number:</p>
                                <p class="font-weight-bold">{{ $invoice->invoice_number }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="text-muted mb-1">Amount:</p>
                                <p class="font-weight-bold">₦{{ number_format($invoice->amount, 2) }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="text-muted mb-1">Student:</p>
                                <p class="font-weight-bold">{{ $invoice->student->user->full_name }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="text-muted mb-1">Payment Type:</p>
                                <p class="font-weight-bold">{{ $invoice->paymentType->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.payments.process-manual') }}" method="POST" enctype="multipart/form-data"
                    id="paymentProofForm">
                    @csrf
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                    <div class="row">
                        <!-- Basic Payment Details -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="bank_name" class="form-label">Bank Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="bank_name" id="bank_name"
                                    class="form-control @error('bank_name') is-invalid @enderror" required value="{{ old('bank_name') }}">
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="transaction_reference" class="form-label">Transaction Reference <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="transaction_reference" id="transaction_reference"
                                    class="form-control @error('transaction_reference') is-invalid @enderror" required  value="{{ old('transaction_reference') }}">
                                @error('transaction_reference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="proof_file" class="form-label">Payment Proof <span
                                        class="text-danger">*</span></label>
                                <input type="file" name="proof_file" id="proof_file"
                                    class="form-control @error('proof_file') is-invalid @enderror" required>
                                <small class="form-text text-muted">Upload payment receipt or bank transfer proof (PDF, JPG,
                                    PNG). Max size: 2MB</small>
                                @error('proof_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="additional_notes" class="form-label">Additional Notes</label>
                                <textarea name="additional_notes" id="additional_notes" rows="3"
                                    class="form-control @error('additional_notes') is-invalid @enderror">{{ old('additional_notes') }}</textarea>
                                @error('additional_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Metadata Fields Section -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Additional Information</h5>
                            <div id="metadata-fields">
                                <!-- Existing metadata fields will be loaded here -->
                                @if (isset($paymentMethod) && isset($paymentMethod->config['required_fields']))
                                    @foreach ($paymentMethod->config['required_fields'] as $field)
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label
                                                        class="form-label">{{ $field['label'] ?? ucwords(str_replace('_', ' ', $field['name'])) }}</label>
                                                    @switch($field['type'] ?? 'text')
                                                        @case('select')
                                                            <select name="metadata[{{ $field['name'] }}]" class="form-control"
                                                                {{ $field['required'] ?? false ? 'required' : '' }}>
                                                                <option value="">Select
                                                                    {{ $field['label'] ?? ucwords(str_replace('_', ' ', $field['name'])) }}
                                                                </option>
                                                                @foreach ($field['options'] ?? [] as $option)
                                                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        @break

                                                        @case('textarea')
                                                            <textarea name="metadata[{{ $field['name'] }}]" class="form-control" rows="3"
                                                                {{ $field['required'] ?? false ? 'required' : '' }}></textarea>
                                                        @break

                                                        @case('date')
                                                            <input type="date" name="metadata[{{ $field['name'] }}]"
                                                                class="form-control"
                                                                {{ $field['required'] ?? false ? 'required' : '' }}>
                                                        @break

                                                        @default
                                                            <input type="{{ $field['type'] ?? 'text' }}"
                                                                name="metadata[{{ $field['name'] }}]" class="form-control"
                                                                {{ $field['required'] ?? false ? 'required' : '' }}>
                                                    @endswitch
                                                    @if (isset($field['description']))
                                                        <small
                                                            class="form-text text-muted">{{ $field['description'] }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <!-- Add New Metadata Field Button -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addMetadataField">
                                        <i class="fas fa-plus"></i> Add More Information
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="float-end">
                                <button type="button" onclick="history.back()" class="btn btn-secondary me-2">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Submit Payment Proof
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const metadataFields = document.getElementById('metadata-fields');
        const addButton = document.getElementById('addMetadataField');
        let fieldCount = document.querySelectorAll('#metadata-fields .row').length;

        addButton.addEventListener('click', function() {
            const newFieldRow = document.createElement('div');
            newFieldRow.className = 'row mb-3 metadata-field-row';
            newFieldRow.innerHTML = `
                <div class="col-md-5">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Field Name"
                               name="metadata_keys[${fieldCount}]" required>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Field Value"
                               name="metadata_values[${fieldCount}]" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-field">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;

            metadataFields.appendChild(newFieldRow);
            fieldCount++;

            // Add remove functionality to the new row
            newFieldRow.querySelector('.remove-field').addEventListener('click', function() {
                newFieldRow.remove();
            });
        });

        // Handle existing remove buttons
        document.querySelectorAll('.remove-field').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.metadata-field-row').remove();
            });
        });
    });
</script>
@endsection
