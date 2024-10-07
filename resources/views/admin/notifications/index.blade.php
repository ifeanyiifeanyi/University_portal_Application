@extends('admin.layouts.admin')

@section('title', 'Notification Manager')

@section('css')
    <style>
        .notification-item {
            border-left: 4px solid #3498db;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            transition: background-color 0.3s;
        }

        .notification-item:hover {
            background-color: #e9ecef;
        }

        .notification-item.unread {
            border-left-color: #e74c3c;
            background-color: #f1f8ff;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .notification-actions {
            display: flex;
            gap: 10px;
        }

        .notification-type {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .notification-details {
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('admin')
    @include('admin.alert')

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Notifications</h4>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Unread Notifications: <span id="unread-count">{{ $unreadCount }}</span></h5>
                <button id="mark-all-read" class="btn btn-primary btn-sm">Mark All as Read</button>
            </div>

            <div id="notifications-container">
                @foreach ($notifications as $notification)
                    <div class="notification-item {{ $notification->read_at ? '' : 'unread' }}"
                        data-id="{{ $notification->id }}">
                        <div class="notification-header">
                            <h6 class="mb-0">{{ $notification->data['student_name'] ?? 'System Notification' }}</h6>
                            <small>{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="notification-type">
                            @if ($notification->type === 'App\Notifications\AdminPaymentNotification')
                                Admin Payment Notification
                            @elseif ($notification->type === 'App\Notifications\PaymentProcessed')
                                Student Payment Processed
                            @else
                                {{ class_basename($notification->type) }}
                            @endif
                        </div>
                        <div class="notification-details">
                            @if (isset($notification->data['payment_type']))
                                <p><strong>Payment Type:</strong> {{ $notification->data['payment_type'] }}</p>
                                <p><strong>Amount:</strong> ₦{{ number_format($notification->data['amount'], 2) }}</p>
                                <p><strong>Reference:</strong> {{ $notification->data['transaction_reference'] }}</p>
                                <p><strong>Status:</strong> {{ ucfirst($notification->data['payment_status']) }}</p>
                            @elseif(isset($notification->data['message']))
                                <p>{{ $notification->data['message'] }}</p>
                            @endif
                        </div>
                        <div class="notification-actions">
                            @if (!$notification->read_at)
                                <button class="mark-as-read btn btn-sm btn-outline-primary"
                                    data-id="{{ $notification->id }}">Mark as Read</button>
                            @endif
                            @if (isset($notification->data['payment_id']))
                                <a href="{{ route('admin.notifications.view', $notification->id) }}"
                                    class="btn btn-sm btn-outline-info">View Details</a>
                            @endif
                            <button class="delete-notification btn btn-sm btn-outline-danger"
                                data-id="{{ $notification->id }}">Delete</button>
                        </div>
                    </div>
                @endforeach
            </div>

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
                        $('.notification-item').removeClass('unread');
                        $('.mark-as-read').remove();
                        updateUnreadCount();
                    }
                });
            });

            // Delete Notification
            $(document).on('click', '.delete-notification', function() {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this notification?')) {
                    $.ajax({
                        url: '{{ route('admin.notifications.destroy', ':id') }}'.replace(':id',
                            id),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $(`[data-id="${id}"]`).remove();
                            updateUnreadCount();
                        }
                    });
                }
            });

            function updateUnreadCount() {
                var unreadCount = $('.notification-item.unread').length;
                $('#unread-count').text(unreadCount);
            }
        });
    </script>
@endsection
