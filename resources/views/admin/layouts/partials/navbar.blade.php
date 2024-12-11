@php
      $recentActivities = \Spatie\Activitylog\Models\Activity::latest()->take(10)->get();
      $totalActivities = \Spatie\Activitylog\Models\Activity::count();
@endphp
<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand">
            <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
            </div>
            <div class="search-bar flex-grow-1">
                <div class="position-relative search-bar-box">
                    <input type="text" class="form-control search-control" placeholder="Type to search..."> <span
                        class="position-absolute top-50 search-show translate-middle-y"><i
                            class='bx bx-search'></i></span>
                    <span class="position-absolute top-50 search-close translate-middle-y"><i
                            class='bx bx-x'></i></span>
                </div>
            </div>
            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item mobile-search-icon">
                        <a class="nav-link" href="#"> <i class='bx bx-search'></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false"> <i class='bx bx-category'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="row row-cols-3 g-3 p-3">
                                <div class="col text-center">
                                    <div class="app-box mx-auto bg-gradient-cosmic text-white"><i
                                            class='bx bx-group'></i>
                                    </div>
                                    <div class="app-title">Teams</div>
                                </div>
                                <div class="col text-center">
                                    <div class="app-box mx-auto bg-gradient-burning text-white"><i
                                            class='bx bx-atom'></i>
                                    </div>
                                    <div class="app-title">Projects</div>
                                </div>
                                <div class="col text-center">
                                    <div class="app-box mx-auto bg-gradient-lush text-white"><i
                                            class='bx bx-shield'></i>
                                    </div>
                                    <div class="app-title">Tasks</div>
                                </div>
                                <div class="col text-center">
                                    <div class="app-box mx-auto bg-gradient-kyoto text-dark"><i
                                            class='bx bx-notification'></i>
                                    </div>
                                    <div class="app-title">Feeds</div>
                                </div>
                                <div class="col text-center">
                                    <div class="app-box mx-auto bg-gradient-blues text-dark"><i class='bx bx-file'></i>
                                    </div>
                                    <div class="app-title">Files</div>
                                </div>
                                <div class="col text-center">
                                    <div class="app-box mx-auto bg-gradient-moonlit text-white"><i
                                            class='bx bx-filter-alt'></i>
                                    </div>
                                    <div class="app-title">Alerts</div>
                                </div>
                            </div>
                        </div>
                    </li>


                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="alert-count" id="notification-count">0</span>
                            <i class='bx bx-bell'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:;">
                                <div class="msg-header">
                                    <p class="msg-header-title">Notifications</p>
                                    <p class="msg-header-clear ms-auto">Marks all as read</p>
                                </div>
                            </a>
                            <div class="header-notifications-list" id="notification-list">
                                <!-- Notifications will be dynamically inserted here -->
                            </div>
                            <a href="{{ route('admin.notification.view') }}">
                                <div class="text-center msg-footer">View All Notifications</div>
                            </a>
                        </div>
                    </li>



                    <!-- Navigation Bar Snippet -->
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="alert-count">{{ $totalActivities }}</span>
                            <i class='bx bx-comment'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:;">
                                <div class="msg-header">
                                    <p class="msg-header-title">Recent Activities</p>
                                    <p class="msg-header-clear ms-auto">View All</p>
                                </div>
                            </a>
                            <div class="header-message-list">
                                @foreach ($recentActivities as $activity)
                                    <a class="dropdown-item" href="javascript:;">
                                        <div class="d-flex align-items-center">
                                            <div class="user-online">
                                                <i class="fas fa-bell fa-2x fa-fw text-muted"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="msg-name">{{ $activity->causer->name }} <span
                                                        class="msg-time float-end">{{ $activity->created_at->diffForHumans() }}</span>
                                                </h6>
                                                <p class="msg-info">{{ $activity->description }}</p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                            <a href="{{ route('activities.index') }}">
                                <div class="text-center msg-footer">View All Activities</div>
                            </a>
                        </div>
                    </li>
                    <!-- End of Navigation Bar Snippet -->
                </ul>
            </div>
            <div class="user-box dropdown">
                <a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ auth()->user()->profileImage() }}" class="user-img" alt="user avatar">
                    <div class="user-info ps-3">
                        <p class="user-name mb-0">{{ auth()->user()->first_name }}</p>
                        <p class="designattion mb-0">{{ auth()->user()->admin->role }}</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('admin.view.profile') }}"><i
                                class="bx bx-user"></i><span>Profile</span></a>
                    </li>
                    <li><a class="dropdown-item" href="{{ route('admin.backups.index') }}"><i
                                class="bx bx-cog"></i><span>Settings</span></a>
                    </li>
                    <li><a class="dropdown-item" href="{{ route('admin.support_tickets.index') }}"><i
                        class="bx bx-cog"></i><span>Student Support Tickets</span></a>
            </li>
                    <li>
                        <div class="dropdown-divider mb-0"></div>
                    </li>
                    <li class="d-flex justify-content-center p-2">
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button class='btn btn-info btn-sm bx bx-log-out-circle'><span>Logout</span></button>
                        </form>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
<script src="{{ asset('') }}assets/js/jquery.min.js"></script>
<script>
    function updateNotifications() {
        $.ajax({
            url: '{{ route('admin.notifications.latest') }}',
            method: 'GET',
            success: function(response) {
                $('#notification-count').text(response.unreadCount);
                var notificationList = $('#notification-list');
                notificationList.empty();

                response.notifications.forEach(function(notification) {
                    var notificationHtml = `
                    <a class="dropdown-item" href="${getNotificationLink(notification)}">
                        <div class="d-flex align-items-center">
                            <div class="notify ${notification.read_at ? 'bg-light-primary text-primary' : 'bg-light-danger text-danger'}">
                                <i class="bx bx-group"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="msg-name">${notification.data.student_name || 'System Notification'}<span class="msg-time float-end">${timeSince(new Date(notification.created_at))}</span></h6>
                                <p class="msg-info">${getNotificationMessage(notification)}</p>
                            </div>
                        </div>
                    </a>
                `;
                    notificationList.append(notificationHtml);
                });
            },
            error: function(xhr, status, error) {
                console.error("Error fetching notifications:", error);
            }
        });
    }

    function getNotificationLink(notification) {
        if (notification.data.payment_id) {
            return '{{ route('admin.notifications.view', ':id') }}'.replace(':id', notification.data.payment_id);
        }
        return '{{ route('admin.notification.view') }}';
    }

    function getNotificationMessage(notification) {
        if (notification.data.payment_type) {
            return `Payment processed: ${notification.data.payment_type}`;
        } else if (notification.data.message) {
            return notification.data.message;
        }
        return notification.type;
    }

    function timeSince(date) {
        var seconds = Math.floor((new Date() - date) / 1000);
        var interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + " years ago";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + " months ago";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + " days ago";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + " hours ago";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + " minutes ago";
        return Math.floor(seconds) + " seconds ago";
    }

    // Update notifications every 60 seconds
    setInterval(updateNotifications, 60000);

    // Initial update
    $(document).ready(function() {
        updateNotifications();
    });
</script>
