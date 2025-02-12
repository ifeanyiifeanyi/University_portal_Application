@extends('admin.layouts.admin')

@section('title', 'Notification Manager')

@section('css')
    <style>
        .notification-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .notification-card {
            display: flex;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            background-color: #fff;
            transition: box-shadow 0.3s, transform 0.3s;
        }

        .notification-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .notification-card.unread {
            border-left: 4px solid #e74c3c;
            background-color: #f9f9f9;
        }

        .notification-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            background-color: #f8f9fa;
            border-right: 1px solid #e0e0e0;
            border-radius: 10px 0 0 10px;
        }

        .notification-icon i {
            font-size: 24px;
            color: #3498db;
        }

        .notification-content {
            flex: 1;
            padding: 15px;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .notification-header h6 {
            margin: 0;
            font-size: 16px;
            color: #2c3e50;
        }

        .notification-header small {
            color: #7f8c8d;
            font-size: 12px;
        }

        .notification-details {
            margin-bottom: 10px;
        }

        .notification-details p {
            margin: 5px 0;
            color: #34495e;
        }

        .notification-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination .page-item.active .page-link {
            background-color: #3498db;
            border-color: #3498db;
        }

        .pagination .page-link {
            color: #3498db;
        }

        .pagination .page-link:hover {
            color: #3498db;
            background-color: #f8f9fa;
        }
    </style>
@endsection

@section('admin')
    @include('admin.alert')

    <div class="card shadow">
       
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Unread Notifications: <span id="unread-count">{{ $unreadCount }}</span></h5>
                <button id="mark-all-read" class="btn btn-primary btn-sm">Mark All as Read</button>
            </div>

            <div class="notification-list">
                @foreach ($notifications as $notification)
                    <div class="notification-card {{ $notification->read_at ? '' : 'unread' }}"
                        data-id="{{ $notification->id }}">
                        <!-- Notification Icon -->
                        <div class="notification-icon">
                            @if ($notification->type === 'App\Notifications\AdminPaymentNotification')
                                <i class="fas fa-bell"></i>
                            @elseif ($notification->type === 'App\Notifications\PaymentProcessed')
                                <i class="fas fa-check-circle"></i>
                            @else
                                <i class="fas fa-info-circle"></i>
                            @endif
                        </div>

                        <!-- Notification Content -->
                        <div class="notification-content">
                            <div class="notification-header">
                                <h6>{{ $notification->data['student_name'] ?? 'System Notification' }}</h6>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="notification-details">
                                @if (isset($notification->data['payment_type']))
                                    <p><strong>Payment Type:</strong> {{ $notification->data['payment_type'] }}</p>
                                    <p><strong>Amount:</strong>
                                        @php
                                            // Sanitize the amount by removing the currency symbol and commas
                                            $amount = str_replace(['₦', ','], '', $notification->data['amount'] ?? '0');
                                            // Convert the sanitized string to a float
                                            $amount = (float)$amount;
                                            // Format the amount with 2 decimal places
                                            echo '₦' . number_format($amount, 2);
                                        @endphp
                                    </p>
                                    <p><strong>Reference:</strong> {{ $notification->data['transaction_reference'] }}</p>
                                    <p><strong>Status:</strong> {{ ucfirst($notification->data['payment_status']) }}</p>
                                @elseif(isset($notification->data['message']))
                                    <p>{{ $notification->data['message'] }}</p>
                                @endif
                            </div>
                            <div class="notification-actions">
                                @if (!$notification->read_at)
                                    <button class="mark-as-read btn btn-sm btn-outline-primary"
                                        data-id="{{ $notification->id }}">
                                        <i class="fas fa-check"></i> Mark as Read
                                    </button>
                                @endif
                                @if (isset($notification->data['payment_id']))
                                    <a href="{{ route('admin.notifications.view', $notification->id) }}"
                                        class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                @endif
                                <button class="delete-notification btn btn-sm btn-outline-danger"
                                    data-id="{{ $notification->id }}">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            // Mark as Read
            $(document).on('click', '.mark-as-read', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: '{{ route('admin.notifications.markAsRead', ':id') }}'.replace(':id', id),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $(`[data-id="${id}"]`).removeClass('unread');
                        $(`[data-id="${id}"] .mark-as-read`).remove();
                        updateUnreadCount();
                    }
                });
            });

            // Mark All as Read
            $('#mark-all-read').click(function() {
                $.ajax({
                    url: '{{ route('admin.notifications.markAllAsRead') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('.notification-card').removeClass('unread');
                        $('.mark-as-read').remove();
                        updateUnreadCount();
                    }
                });
            });

            // Delete Notification
            $(document).on('click', '.delete-notification', function() {
                var id = $(this).data('id');

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
                        $.ajax({
                            url: '{{ route('admin.notifications.destroy', ':id') }}'
                                .replace(':id', id),
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    xhr.responseJSON?.message ||
                                    'Something went wrong!',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });


        });
    </script>
@endsection
