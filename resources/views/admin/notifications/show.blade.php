@extends('admin.layouts.admin')

@section('title', 'Notification Details')

@section('admin')
    @include('admin.alert')

    <div class="container">
        <div class="col-md-8 mx-auto">
            <!-- Notification Header -->
            <div class="notification-header mb-4">
                <h5 class="mb-2">
                    @if ($notification->type === 'App\Notifications\AdminPaymentNotification')
                        <i class="fas fa-bell text-primary"></i> Admin Payment Notification
                    @elseif ($notification->type === 'App\Notifications\PaymentProcessed')
                        <i class="fas fa-check-circle text-success"></i> Student Payment Processed
                    @else
                        <i class="fas fa-info-circle text-info"></i> {{ class_basename($notification->type) }}
                    @endif
                </h5>
                <small class="text-muted">
                    <i class="fas fa-calendar-alt"></i> {{ $notification->created_at->format('F j, Y, g:i a') }}
                </small>
            </div>

            <!-- Notification Body -->
            <div class="notification-body bg-light p-4 rounded">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-3">
                            <strong><i class="fas fa-user"></i> Student Name:</strong>
                            <span class="text-dark">{{ $notification->data['student_name'] ?? 'N/A' }}</span>
                        </p>
                        <p class="mb-3">
                            <strong><i class="fas fa-money-bill-wave"></i> Payment Type:</strong>
                            <span class="text-dark">{{ $notification->data['payment_type'] ?? 'N/A' }}</span>
                        </p>
                        <p class="mb-3">
                            <strong><i class="fas fa-coins"></i> Amount:</strong>
                            <span class="text-dark">
                                @php
                                    // Sanitize the amount by removing the currency symbol and commas
                                    $amount = str_replace(['₦', ','], '', $notification->data['amount'] ?? '0');
                                    // Convert the sanitized string to a float
                                    $amount = (float)$amount;
                                    // Format the amount with 2 decimal places
                                    echo '₦' . number_format($amount, 2);
                                @endphp
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-3">
                            <strong><i class="fas fa-receipt"></i> Transaction Reference:</strong>
                            <span class="text-dark">{{ $notification->data['transaction_reference'] ?? 'N/A' }}</span>
                        </p>
                        <p class="mb-3">
                            <strong><i class="fas fa-check-circle"></i> Payment Status:</strong>
                            <span class="text-dark">{{ ucfirst($notification->data['payment_status'] ?? 'N/A') }}</span>
                        </p>
                        <p class="mb-3">
                            <strong><i class="fas fa-file-invoice"></i> Invoice Status:</strong>
                            <span class="text-dark">{{ ucfirst($notification->data['invoice_status'] ?? 'N/A') }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notification Footer -->
            <div class="notification-footer mt-4">
                <p class="mb-0">
                    <strong><i class="fas fa-eye"></i> Read Status:</strong>
                    @if ($notification->read_at)
                        <span class="text-success">Read on {{ $notification->read_at->format('F j, Y, g:i a') }}</span>
                    @else
                        <span class="text-danger">Unread</span>
                    @endif
                </p>
            </div>

            <!-- Back Button -->
            <div class="text-center mt-4">
                <a href="{{ route('admin.notification.view') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Notifications
                </a>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .notification-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .notification-body {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .notification-body p {
            margin-bottom: 10px;
        }

        .notification-footer {
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }

        .card-header {
            border-radius: 8px 8px 0 0;
        }

        .card {
            border-radius: 8px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .text-dark {
            color: #343a40 !important;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }
    </style>
@endsection
