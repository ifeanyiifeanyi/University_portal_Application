@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('css')
    <style>
        .stat-card {
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
@endsection

@section('admin')
<div id="payment-status-container"></div>

    <div class="page-content">
        <!-- User Statistics Row -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 mb-4">
            <div class="col">
                <div class="card radius-10 bg-gradient-deepblue stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 text-white">{{ \App\Models\User::count() }}</h5>
                            <div class="ms-auto">
                                <i class='bx bx-user fs-3 text-white'></i>
                            </div>
                        </div>
                        <div class="progress my-3 bg-light-transparent" style="height:3px;">
                            <div class="progress-bar bg-white" role="progressbar" style="width: 100%" aria-valuenow="100"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex align-items-center text-white">
                            <p class="mb-0">Total Users</p>
                            <p class="mb-0 ms-auto">
                                <i class='bx bx-group'></i>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card radius-10 bg-gradient-orange stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 text-white">{{ \App\Models\Teacher::count() }}</h5>
                            <div class="ms-auto">
                                <i class='bx bx-user-voice fs-3 text-white'></i>
                            </div>
                        </div>
                        <div class="progress my-3 bg-light-transparent" style="height:3px;">
                            <div class="progress-bar bg-white" role="progressbar" style="width: 100%" aria-valuenow="100"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex align-items-center text-white">
                            <p class="mb-0">Total Teachers</p>
                            <p class="mb-0 ms-auto">
                                <i class='bx bx-book-reader'></i>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card radius-10 bg-gradient-ohhappiness stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 text-white">{{ \App\Models\Student::count() }}</h5>
                            <div class="ms-auto">
                                <i class='bx bx-user-pin fs-3 text-white'></i>
                            </div>
                        </div>
                        <div class="progress my-3 bg-light-transparent" style="height:3px;">
                            <div class="progress-bar bg-white" role="progressbar" style="width: 100%" aria-valuenow="100"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex align-items-center text-white">
                            <p class="mb-0">Total Students</p>
                            <p class="mb-0 ms-auto">
                                <i class='bx bx-graduation'></i>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card radius-10 bg-gradient-ibiza stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 text-white">{{ \App\Models\Department::count() }}</h5>
                            <div class="ms-auto">
                                <i class='bx bx-building fs-3 text-white'></i>
                            </div>
                        </div>
                        <div class="progress my-3 bg-light-transparent" style="height:3px;">
                            <div class="progress-bar bg-white" role="progressbar" style="width: 100%" aria-valuenow="100"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex align-items-center text-white">
                            <p class="mb-0">Departments</p>
                            <p class="mb-0 ms-auto">
                                <i class='bx bx-buildings'></i>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Information Row -->
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">Academic Overview</h6>
                            </div>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table align-middle mb-0">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class='bx bx-book-open mr-2'></i>
                                                <div class="ms-2">
                                                    <h6 class="mb-0">Total Courses</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ \App\Models\Course::count() }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class='bx bx-calendar mr-2'></i>
                                                <div class="ms-2">
                                                    <h6 class="mb-0">Course Enrollments</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ \App\Models\CourseEnrollment::count() }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class='bx bx-time-five mr-2'></i>
                                                <div class="ms-2">
                                                    <h6 class="mb-0">Active Timetables</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ \App\Models\TimeTable::count() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">Financial Overview</h6>
                            </div>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table align-middle mb-0">
                                <tbody>
                                    <tr>
                                        <td>Total Payments</td>
                                        <td>{{ \App\Models\Payment::count() }}</td>
                                    </tr>
                                    <tr>
                                        <td>Payment Types</td>
                                        <td>{{ \App\Models\PaymentType::count() }}</td>
                                    </tr>
                                    <tr>
                                        <td>Recent Transactions</td>
                                        <td>{{ \App\Models\Payment::whereDate('created_at', \Carbon\Carbon::today())->count() }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Activity Row -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">Recent System Activity</h6>
                            </div>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Activity Type</th>
                                        <th>Count Today</th>
                                        <th>Total Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Score Updates</td>
                                        <td>{{ \App\Models\StudentScore::whereDate('created_at', \Carbon\Carbon::today())->count() }}
                                        </td>
                                        <td>{{ \App\Models\StudentScore::count() }}</td>
                                    </tr>
                                    <tr>
                                        <td>Attendance Records</td>
                                        <td>{{ \App\Models\Attendance::whereDate('created_at', \Carbon\Carbon::today())->count() }}
                                        </td>
                                        <td>{{ \App\Models\Attendance::count() }}</td>
                                    </tr>
                                    <tr>
                                        <td>Teacher Assignments</td>
                                        <td>{{ \App\Models\TeacherAssignment::whereDate('created_at', \Carbon\Carbon::today())->count() }}
                                        </td>
                                        <td>{{ \App\Models\TeacherAssignment::count() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        // Add this to your dashboard page JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we have a payment reference in the URL
            const urlParams = new URLSearchParams(window.location.search);
            const reference = urlParams.get('reference');

            if (reference) {
                // Show a loading message
                const statusContainer = document.getElementById('payment-status-container');
                if (statusContainer) {
                    statusContainer.innerHTML = '<div class="alert alert-info">Verifying your payment...</div>';
                }

                // Verify the payment
                fetch(`/payment/verify?reference=${reference}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            // Payment successful
                            if (statusContainer) {
                                statusContainer.innerHTML = `
                            <div class="alert alert-success">
                                <h4>Payment Successful!</h4>
                                <p>Amount: ₦${data.data.amount}</p>
                                <p>Reference: ${data.data.reference}</p>
                            </div>
                        `;
                            }
                        } else {
                            // Payment failed
                            if (statusContainer) {
                                statusContainer.innerHTML = `
                            <div class="alert alert-danger">
                                <h4>Payment Verification Failed</h4>
                                <p>${data.message}</p>
                            </div>
                        `;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Payment verification error:', error);
                        if (statusContainer) {
                            statusContainer.innerHTML = `
                        <div class="alert alert-danger">
                            <h4>Error Verifying Payment</h4>
                            <p>Please contact support or try again later.</p>
                        </div>
                    `;
                        }
                    });
            }
        });
    </script>
@endsection
