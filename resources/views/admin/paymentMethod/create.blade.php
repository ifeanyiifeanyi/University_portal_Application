@extends('admin.layouts.admin')

@section('title', 'Create Payment Method')

@section('css')
<style>
    .config-fields, #gatewaySelect {
        display: none;
    }
</style>
@endsection

@section('admin')
<div class="container">
    <div>
        @include('admin.return_btn')
    </div>
    <hr />
    <div class="row">
        <div class="col-md-7 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.payment_method.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name">Payment Method Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                            @error('name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="logo">Logo</label>
                            <input type="file" class="form-control" id="logo" name="logo">
                            @error('logo')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="payment_type">Payment Type</label>
                            <select class="form-control" id="payment_type" name="config[payment_type]">
                                <option value="">Select a type</option>
                                <option value="credit_card" {{ old('config.payment_type') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="bank_transfer" {{ old('config.payment_type') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="paypal" {{ old('config.payment_type') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                <option value="cash" {{ old('config.payment_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                            </select>
                            @error('config.payment_type')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="gatewaySelect" class="form-group mb-3">
                            <label for="gateway">Payment Gateway</label>
                            <select class="form-control" id="gateway" name="config[gateway]">
                                <option value="">Select a gateway</option>
                                <option value="paystack" {{ old('config.gateway') == 'paystack' ? 'selected' : '' }}>Paystack</option>
                                <option value="flutterwave" {{ old('config.gateway') == 'flutterwave' ? 'selected' : '' }}>Flutterwave</option>
                                <option value="remita" {{ old('config.gateway') == 'remita' ? 'selected' : '' }}>Remita</option>
                            </select>
                            @error('config.gateway')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="is_active">
                                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                                Active
                            </label>
                        </div>

                        <div id="configFields" class="config-fields">
                            <!-- Dynamic config fields will be inserted here -->
                        </div>

                        <button type="submit" class="btn btn-primary">Create Payment Method</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentTypeSelect = document.getElementById('payment_type');
        const gatewaySelect = document.getElementById('gatewaySelect');
        const gatewayDropdown = document.getElementById('gateway');
        const configFields = document.getElementById('configFields');

        paymentTypeSelect.addEventListener('change', updateFields);
        gatewayDropdown.addEventListener('change', updateConfigFields);

        function updateFields() {
            const type = paymentTypeSelect.value;
            if (type === 'credit_card') {
                gatewaySelect.style.display = 'block';
                updateConfigFields();
            } else {
                gatewaySelect.style.display = 'none';
                updateConfigFields();
            }
        }

        function updateConfigFields() {
            const type = paymentTypeSelect.value;
            const gateway = gatewayDropdown.value;
            let fields = [];

            if (type === 'credit_card' && gateway) {
                switch (gateway) {
                    case 'paystack':
                        fields = [
                            { name: 'public_key', label: 'Paystack Public Key', type: 'text', required: true },
                            { name: 'secret_key', label: 'Paystack Secret Key', type: 'text', required: true }
                        ];
                        break;
                    case 'flutterwave':
                        fields = [
                            { name: 'public_key', label: 'Flutterwave Public Key', type: 'text', required: true },
                            { name: 'secret_key', label: 'Flutterwave Secret Key', type: 'text', required: true },
                            { name: 'encryption_key', label: 'Flutterwave Encryption Key', type: 'text', required: true }
                        ];
                        break;
                    case 'remita':
                        fields = [
                            { name: 'merchant_id', label: 'Remita Merchant ID', type: 'text', required: true },
                            { name: 'api_key', label: 'Remita API Key', type: 'text', required: true },
                            { name: 'service_type_id', label: 'Remita Service Type ID', type: 'text', required: true }
                        ];
                        break;
                }
            } else {
                switch (type) {
                    case 'bank_transfer':
                        fields = [
                            { name: 'account_number', label: 'Account Number', type: 'text', required: true },
                            { name: 'bank_name', label: 'Bank Name', type: 'text', required: true }
                        ];
                        break;
                    case 'paypal':
                        fields = [
                            { name: 'client_id', label: 'PayPal Client ID', type: 'text', required: true },
                            { name: 'client_secret', label: 'PayPal Client Secret', type: 'text', required: true }
                        ];
                        break;
                    case 'cash':
                        fields = [
                            { name: 'cash_drawer_id', label: 'Cash Drawer ID', type: 'text', required: true }
                        ];
                        break;
                }
            }

            configFields.innerHTML = '';
            fields.forEach(field => {
                const div = document.createElement('div');
                div.className = 'form-group mb-3';
                div.innerHTML = `
                    <label for="config_${field.name}">${field.label}</label>
                    <input type="${field.type}" class="form-control" id="config_${field.name}" name="config[${field.name}]" ${field.required ? 'required' : ''}>
                `;
                configFields.appendChild(div);
            });

            configFields.style.display = fields.length > 0 ? 'block' : 'none';
        }

        // Initial call to set up fields based on any pre-selected values
        updateFields();
    });
</script>
@endsection
