@extends('admin.layouts.admin')

@section('title', 'Payment Method Details')

@section('css')
<style>
    .detail-card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 10px;
    }
    .config-item {
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
        transition: background-color 0.3s;
    }
    .config-item:last-child {
        border-bottom: none;
    }
    .config-item:hover {
        background-color: #f8f9fa;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
    }
    .detail-section {
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
    }
    .detail-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
</style>
@endsection

@section('admin')
    @include('admin.alert')

    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card detail-card">
                <div class="card-body p-4">
                    <!-- Header Section -->
                    <div class="d-flex justify-content-between align-items-center detail-section">
                        <div>
                            <h2 class="card-title mb-1">
                                <i class="fas fa-credit-card me-2"></i>
                                {{ Str::title($paymentMethod->name) }}
                            </h2>
                            <span class="status-badge {{ $paymentMethod->is_active ? 'bg-success' : 'bg-danger' }} text-white">
                                <i class="fas {{ $paymentMethod->is_active ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                {{ $paymentMethod->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        @if ($paymentMethod->logo)
                            <div class="text-end">
                                <img src="{{ asset('storage/' . $paymentMethod->logo) }}"
                                     alt="{{ $paymentMethod->name }} Logo"
                                     class="img-fluid rounded"
                                     style="max-width: 120px; height: auto;">
                            </div>
                        @endif
                    </div>

                    <!-- Basic Information Section -->
                    <div class="detail-section">
                        <h4 class="mb-3">
                            <i class="fas fa-info-circle me-2"></i>Basic Information
                        </h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-3">
                                    <i class="fas fa-money-bill-wave me-2 text-muted"></i>
                                    <strong>Payment Type:</strong><br>
                                    <span class="ms-4">{{ str_replace('_', ' ', Str::title($paymentMethod->config['payment_type'])) ?? 'N/A' }}</span>
                                </p>
                            </div>
                            @if ($paymentMethod->config['payment_type'] == 'credit_card')
                            <div class="col-md-6">
                                <p class="mb-3">
                                    <i class="fas fa-network-wired me-2 text-muted"></i>
                                    <strong>Gateway:</strong><br>
                                    <span class="ms-4">{{ Str::title($paymentMethod->config['gateway']) ?? 'N/A' }}</span>
                                </p>
                            </div>
                            @endif
                        </div>
                        @if($paymentMethod->description)
                        <div class="mt-2">
                            <p class="mb-0">
                                <i class="fas fa-align-left me-2 text-muted"></i>
                                <strong>Description:</strong><br>
                                <span class="ms-4">{{ $paymentMethod->description }}</span>
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Configuration Section -->
                    <div class="detail-section">
                        <h4 class="mb-3">
                            <i class="fas fa-cog me-2"></i>Configuration Details
                        </h4>
                        <div class="bg-light rounded p-3">
                            @foreach ($paymentMethod->config as $key => $value)
                                @if ($key != 'payment_type' && $key != 'gateway')
                                    <div class="config-item">
                                        <i class="fas fa-angle-right me-2 text-muted"></i>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                        <span class="ms-2">{{ $value }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-end mt-4">
                        <a href="{{ route('admin.payment_method.edit', $paymentMethod) }}"
                           class="btn btn-primary me-2">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('admin.payment_method.destroy', $paymentMethod) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                    class="btn btn-danger delete-btn">
                                <i class="fas fa-trash-alt me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteBtn = document.querySelector('.delete-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                const form = this.closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This payment method will be permanently deleted.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }
    });
</script>
@endsection
