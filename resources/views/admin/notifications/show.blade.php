@extends('admin.layouts.admin')

@section('title', 'Notification Details')



@section('admin')
    @include('admin.alert')

    <div class="container">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Notification Details</h4>
                <div class="row">
                    <div class="col-md-12 mx-auto">
                        <div class="notification-detail">
                            <div class="notification-header">
                                {{-- @dump(class_basename($notification->type)) --}}
                                <h5>
                                    @if ($notification->type === 'App\Notifications\AdminPaymentNotification')
                                        Admin Payment Notification
                                    @elseif ($notification->type === 'App\Notifications\PaymentProcessed')
                                        Student Payment Processed
                                    @else
                                        {{ class_basename($notification->type) }}
                                    @endif
                                </h5>
                                <small>{{ $notification->created_at->format('F j, Y, g:i a') }}</small>
                            </div>
                            <div class="notification-body">
                                <p><strong>Student Name:</strong> {{ $notification->data['student_name'] ?? 'N/A' }}</p>
                                <p><strong>Payment Type:</strong> {{ $notification->data['payment_type'] ?? 'N/A' }}</p>
                                <p><strong>Amount:</strong> ₦{{ number_format($notification->data['amount'] ?? 0, 2) }}</p>
                                <p><strong>Transaction Reference:</strong>
                                    {{ $notification->data['transaction_reference'] ?? 'N/A' }}</p>
                                <p><strong>Payment Status:</strong>
                                    {{ ucfirst($notification->data['payment_status'] ?? 'N/A') }}</p>
                                <p><strong>Invoice Status:</strong>
                                    {{ ucfirst($notification->data['invoice_status'] ?? 'N/A') }}</p>
                            </div>
                            <div class="notification-footer">
                                <p><strong>Read Status:</strong>
                                    {{ $notification->read_at ? 'Read on ' . $notification->read_at->format('F j, Y, g:i a') : 'Unread' }}
                                </p>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('admin.notification.view') }}" class="btn btn-primary">Back to
                                Notifications</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <style>
        .notification-detail {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .notification-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .notification-body {
            margin-bottom: 20px;
        }

        .notification-body p {
            margin-bottom: 10px;
        }

        .notification-footer {
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
    </style>
@endsection
