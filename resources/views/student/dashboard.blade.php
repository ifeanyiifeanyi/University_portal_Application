@extends('student.layouts.student')

@section('title', 'Student Dashboard')
@section('css')
<style>
    :root {
        --primary-color: #0d382e;
        --secondary-color: #20c997;
    }
    body {
        background-color: #f4f7f6;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .dashboard-icon {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 15px;
    }

    .dashboard-card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            background: white;
            height: 100%;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        .dashboard-card-header {
            background: linear-gradient(45deg, #2c3e50, #3498db);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1rem 1.5rem;
        }
        
        .timeline {
            position: relative;
            max-height: 500px;
            overflow-y: auto;
        }
        
        .timeline-item {
            padding: 1.5rem;
            border-left: 3px solid #3498db;
            position: relative;
            margin-left: 20px;
            margin-bottom: 1.5rem;
            background: #f8f9fa;
            border-radius: 0 15px 15px 0;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -9px;
            top: 1.5rem;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: #3498db;
        }
        
        .timeline-date {
            color: #666;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .timeline-content {
            margin-top: 0.8rem;
        }
        
        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-paid { background: #d4edda; color: #155724; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-partial { background: #fff3cd; color: #856404; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .status-failed { background: #f8d7da; color: #721c24; }
        .status-cancelled { background: #e2e3e5; color: #383d41; }
        .status-refunded { background: #d1ecf1; color: #0c5460; }

        .payment-info {
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .payment-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.3rem;
        }

        .payment-info-label {
            color: #6c757d;
            font-weight: 500;
        }

        .chart-container {
            position: relative;
            height: 400px;
        }

        .installment-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 0.25rem 0.5rem;
            background: #e3f2fd;
            color: #0d47a1;
            border-radius: 15px;
            font-size: 0.8rem;
        }
</style>
@endsection
@section('student')

<div class="container-fluid">
    @php
    // Create a collection from the teacher's attributes and check if any is empty
    $incompleteProfile = collect($student->getAttributes())->except(['deleted_at','cgpa'])->contains(function ($value) {
        return empty($value);
    });
@endphp

@if($incompleteProfile)
<div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
    <strong>Profile Incomplete!</strong> Please complete your profile to access all features.
    <a href="{{route('student.view.profile')}}" class="btn btn-primary btn-sm ms-3">Complete Profile</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row mt-4">
    <div class="col-12">
        <h2 class="mb-4 text-center">Welcome, {{ $student->user->fullName() }}!</h2>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-money-bill-wave dashboard-icon"></i>
                <h5 class="card-title">Total Fees Paid</h5>
                <p class="card-text fs-4 fw-bold">₦{{ number_format($totalfees, 2) }}</p>
                <a href="{{ route('student.view.payments') }}" class="btn btn-primary w-100">
                    View All Payments
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-graduation-cap dashboard-icon"></i>
                <h5 class="card-title">Academic Performance</h5>
                <p class="card-text fs-4 fw-bold">CGPA: {{ $student->cgpa }}</p>
                <a href="{{ route('student.view.result.select') }}" class="btn btn-primary w-100">
                    View Results
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-id-card dashboard-icon"></i>
                <h5 class="card-title">Virtual ID Card</h5>
                @if($incompleteProfile)
                    <p class="text-warning">Complete profile to view</p>
                @else
                    <p class="card-text">Your digital student ID</p>
                    <a href="{{route('student.view.virtualid')}}" class="btn btn-primary w-100">
                        View ID Card
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-user-edit dashboard-icon"></i>
                <h5 class="card-title">Profile Management</h5>
                <p class="card-text">Update your personal details</p>
                <a href="{{route('student.view.profile')}}" class="btn btn-primary w-100">
                    Edit Profile
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4 class="mb-3">Quick Actions</h4>
        <div class="d-flex gap-3">
            <a href="{{ route('student.view.courseregistration') }}" class="btn btn-outline-primary">
                <i class="fas fa-book me-2"></i>My Courses
            </a>
            <a href="#" class="btn btn-outline-primary">
                <i class="fas fa-calendar-alt me-2"></i>Class Schedule
            </a>
            <a href="#" class="btn btn-outline-primary">
                <i class="fas fa-clipboard-list me-2"></i>Exam Schedule
            </a>
        </div>
    </div>
</div>

  

<div class="row g-4 mt-3">
    <!-- Bar Chart Card -->
    <div class="col-md-6">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="mb-0">Payment Distribution</h5>
            </div>
            <div class="card-body">
                <div id="chartLoading" class="loading-overlay">
                    <div class="loading-spinner"></div>
                </div>
                <canvas id="paymentChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Timeline Card -->
    <div class="col-md-6">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="mb-0">Payment Timeline</h5>
            </div>
            <div class="card-body">
                <div id="timelineLoading" class="loading-overlay">
                    <div class="loading-spinner"></div>
                </div>
                <div class="timeline" id="paymentTimeline"></div>
            </div>
        </div>
    </div>
</div>



                    </div> <!-- container-fluid -->

                    @section('javascript')
<script>
    let paymentChart = null;

    // Function to initialize the dashboard
    async function initializeDashboard() {
        try {
            // Show loading states
            document.getElementById('chartLoading').style.display = 'flex';
            document.getElementById('timelineLoading').style.display = 'flex';
            const studentId = {{ Auth::user()->id }};

            // Fetch data from the controller
            const response = await fetch("{{ route('student.getpayment.data', ':student_id') }}".replace(':student_id', studentId));
        
            const data = await response.json();

            if (data.payments.length === 0) {
                handleEmptyState();
                return;
            }

            // Initialize visualizations
            initializeChart(data.payments);
            initializeTimeline(data.payments);
        } catch (error) {
            console.error('Error loading dashboard:', error);
            handleError();
        } finally {
            // Hide loading states
            document.getElementById('chartLoading').style.display = 'none';
            document.getElementById('timelineLoading').style.display = 'none';
        }
    }

    function initializeChart(payments) {
        const ctx = document.getElementById('paymentChart').getContext('2d');
        
        if (paymentChart) {
            paymentChart.destroy();
        }

        paymentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: payments.map(payment => 
                    new Date(payment.payment_date).toLocaleDateString('en-US', {
                        month: 'short',
                        year: 'numeric'
                    })
                ),
                datasets: [{
                    label: 'Payment Amount',
                    data: payments.map(payment => payment.amount),
                    backgroundColor: payments.map(payment => {
                        const colors = {
                            'paid': '#3498db',
                            'pending': '#f1c40f',
                            'processing': '#2ecc71',
                            'partial': '#e67e22',
                            'rejected': '#e74c3c',
                            'failed': '#c0392b',
                            'cancelled': '#95a5a6',
                            'refunded': '#1abc9c'
                        };
                        return colors[payment.status] || '#bdc3c7';
                    })
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₦' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    function initializeTimeline(payments) {
        const timeline = document.getElementById('paymentTimeline');
        timeline.innerHTML = '';

        payments.forEach(payment => {
            const timelineItem = `
                <div class="timeline-item">
                    ${payment.is_installment ? 
                        '<div class="installment-indicator">Installment Payment</div>' : ''}
                    <div class="timeline-date">
                        ${new Date(payment.payment_date).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}
                    </div>
                    <div class="timeline-content">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${payment.payment_type}</strong>
                                <small class="d-block text-muted">${payment.transaction_reference}</small>
                            </div>
                            <span class="status-badge status-${payment.status.toLowerCase()}">${payment.status}</span>
                        </div>
                        <div class="payment-info mt-3">
                            <div class="row">
                                <div class="col-6">
                                    <strong class="d-block mb-2">Amount: ₦${payment.amount.toLocaleString()}</strong>
                                    <small class="d-block text-muted">Method: ${payment.payment_method}</small>
                                </div>
                                <div class="col-6">
                                    <small class="d-block text-muted">Session: ${payment.academic_session}</small>
                                    <small class="d-block text-muted">Semester: ${payment.semester}</small>
                                    <small class="d-block text-muted">Level: ${payment.level}</small>
                                </div>
                            </div>
                            ${payment.is_installment ? `
                                <div class="mt-2 pt-2 border-top">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="d-block text-muted">Remaining: ₦${payment.remaining_amount.toLocaleString()}</small>
                                            <small class="d-block text-muted">Next Payment: ₦${payment.next_transaction_amount.toLocaleString()}</small>
                                        </div>
                                        <div class="col-6">
                                            <small class="d-block text-muted">Next Due Date: ${new Date(payment.next_installment_date).toLocaleDateString()}</small>
                                            <small class="d-block text-muted">Status: ${payment.installment_status}</small>
                                        </div>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
            
            timeline.innerHTML += timelineItem;
        });
    }

    function handleEmptyState() {
        document.getElementById('paymentChart').innerHTML = `
            <div class="empty-state">
                <h4>No Payment Records</h4>
                <p>There are no payment records available to display.</p>
            </div>
        `;
        
        document.getElementById('paymentTimeline').innerHTML = `
            <div class="empty-state">
                <h4>No Payment History</h4>
                <p>Your payment timeline will appear here once you make payments.</p>
            </div>
        `;
    }

    function handleError() {
        const errorMessage = `
            <div class="empty-state text-danger">
                <h4>Error Loading Data</h4>
                <p>There was an error loading your payment information. Please try again later.</p>
            </div>
        `;
        
        document.getElementById('paymentChart').innerHTML = errorMessage;
        document.getElementById('paymentTimeline').innerHTML = errorMessage;
    }

    // Initialize the dashboard when the page loads
    document.addEventListener('DOMContentLoaded', () => {
        // const studentId = /* Get student ID from your authentication system */;
        // const studentId = {{ Auth::user()->id }};
        initializeDashboard();
    });
</script>
@endsection

@endsection
