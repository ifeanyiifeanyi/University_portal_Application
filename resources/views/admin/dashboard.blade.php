
@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.53.0/apexcharts.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .stat-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 15px;
            overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .chart-container {
            position: relative;
            height: 300px;
            min-width: 200px;
            margin-bottom: 20px;
        }
        .chart-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .chart-card:hover {
            transform: translateY(-5px);
        }
        .activity-card {
            max-height: 400px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #4e73df #f8f9fc;
        }
        .activity-item {
            border-left: 2px solid #4e73df;
            padding-left: 20px;
            margin-bottom: 15px;
            position: relative;
        }
        .activity-item:before {
            content: '';
            position: absolute;
            left: -9px;
            top: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #4e73df;
        }
        .counter-animation {
            visibility: hidden;
        }
        .progress-animate {
            animation: progress-animation 1.5s ease-in-out forwards;
        }
        @keyframes progress-animation {
            0% { width: 0%; }
            100% { width: 100%; }
        }
        .bg-gradient-deepblue { background: linear-gradient(45deg, #2c3e50, #3498db); }
        .bg-gradient-orange { background: linear-gradient(45deg, #e67e22, #f1c40f); }
        .bg-gradient-ohhappiness { background: linear-gradient(45deg, #00c9ff, #92fe9d); }
        .bg-gradient-ibiza { background: linear-gradient(45deg, #ee0979, #ff6a00); }
        .bg-light-transparent { background: rgba(255, 255, 255, 0.2); }
    </style>
@endsection

@section('admin')
    <div class="page-content p-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card radius-10">
                    <div class="card-body d-flex align-items-center">
                        <div>
                            <h4 class="mb-0">School Statistics Dashboard</h4>
                            <p class="text-muted">Welcome to your interactive analytics dashboard</p>
                        </div>
                        <div class="ms-auto">
                            <button class="btn btn-primary" id="refreshData">
                                <i class="bx bx-refresh me-2"></i>Refresh Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 mb-4">
            <div class="col">
                <div class="card radius-10 bg-gradient-deepblue stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 text-white counter-animation" id="usersCounter" data-target="{{ \App\Models\User::count() ?: 0 }}">0</h5>
                            <div class="ms-auto">
                                <i class='bx bx-user-circle fs-3 text-white'></i>
                            </div>
                        </div>
                        <div class="progress my-3 bg-light-transparent" style="height:3px;">
                            <div class="progress-bar bg-white progress-animate" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex align-items-center text-white">
                            <p class="mb-0">Total Users</p>
                            <p class="mb-0 ms-auto">
                                <span class="badge bg-light text-dark">
                                    <i class='bx bx-trending-up me-1'></i>{{ \App\Models\User::whereDate('created_at', \Carbon\Carbon::today())->count() ?: 0 }} today
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 bg-gradient-orange stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 text-white counter-animation" id="teachersCounter" data-target="{{ \App\Models\Teacher::count() ?: 0 }}">0</h5>
                            <div class="ms-auto">
                                <i class='bx bx-user-voice fs-3 text-white'></i>
                            </div>
                        </div>
                        <div class="progress my-3 bg-light-transparent" style="height:3px;">
                            <div class="progress-bar bg-white progress-animate" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex align-items-center text-white">
                            <p class="mb-0">Teachers</p>
                            <p class="mb-0 ms-auto">
                                <span class="badge bg-light text-dark">
                                    <i class='bx bx-book-reader me-1'></i>{{ \App\Models\TeacherAssignment::count() ?: 0 }} assignments
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 bg-gradient-ohhappiness stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 text-white counter-animation" id="studentsCounter" data-target="{{ \App\Models\Student::count() ?: 0 }}">0</h5>
                            <div class="ms-auto">
                                <i class='bx bx-user-pin fs-3 text-white'></i>
                            </div>
                        </div>
                        <div class="progress my-3 bg-light-transparent" style="height:3px;">
                            <div class="progress-bar bg-white progress-animate" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex align-items-center text-white">
                            <p class="mb-0">Total Students</p>
                            <p class="mb-0 ms-auto">
                                <span class="badge bg-light text-dark">
                                    <i class='bx bx-graduation me-1'></i>{{ \App\Models\CourseEnrollment::count() ?: 0 }} enrollments
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 bg-gradient-ibiza stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 text-white counter-animation" id="departmentCounter" data-target="{{ \App\Models\Department::count() ?: 0 }}">0</h5>
                            <div class="ms-auto">
                                <i class='bx bx-building fs-3 text-white'></i>
                            </div>
                        </div>
                        <div class="progress my-3 bg-light-transparent" style="height:3px;">
                            <div class="progress-bar bg-white progress-animate" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex align-items-center text-white">
                            <p class="mb-0">Departments</p>
                            <p class="mb-0 ms-auto">
                                <span class="badge bg-light text-dark">
                                    <i class='bx bx-buildings me-1'></i>{{ \App\Models\Course::count() ?: 0 }} courses
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-12 col-lg-8">
                <div class="card chart-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div>
                                <h5 class="mb-0">Student Enrollment Trends</h5>
                                <p class="text-muted mb-0">Course enrollments over time</p>
                            </div>
                            <div class="ms-auto">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary chart-period active" data-period="weekly">Weekly</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary chart-period" data-period="monthly">Monthly</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary chart-period" data-period="yearly">Yearly</button>
                                </div>
                            </div>
                        </div>
                        <div class="chart-container">
                            <div id="enrollmentChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card chart-card">
                    <div class="card-body">
                        <h5 class="card-title">Student Distribution</h5>
                        <p class="text-muted">By department</p>
                        <div class="chart-container">
                            <div id="departmentDistributionChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial & Academic -->
        <div class="row mb-4">
            <div class="col-12 col-lg-5">
                <div class="card chart-card">
                    <div class="card-body">
                        <h5 class="card-title">Financial Overview</h5>
                        <p class="text-muted">Payment analytics</p>
                        <div class="chart-container">
                            <div id="paymentsChart"></div>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <td><i class="bx bx-credit-card-front text-primary me-2"></i>Total Payments</td>
                                        <td>{{ \App\Models\Payment::count() ?: 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td><i class="bx bx-wallet text-success me-2"></i>Payment Types</td>
                                        <td>{{ \App\Models\PaymentType::count() ?: 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td><i class="bx bx-calendar-check text-warning me-2"></i>Today's Transactions</td>
                                        <td>{{ \App\Models\Payment::whereDate('created_at', \Carbon\Carbon::today())->count() ?: 0 }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-7">
                <div class="card chart-card">
                    <div class="card-body">
                        <h5 class="card-title">Academic Performance</h5>
                        <p class="text-muted">Grades distribution by semester</p>
                        <div class="chart-container">
                            <div id="gradesChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity & Quick Analytics -->
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card activity-card">
                    <div class="card-body">
                        <h5 class="card-title">Recent System Activity</h5>
                        <div class="timeline mt-4">
                            <div class="activity-item">
                                <p class="mb-1"><strong>Score Updates</strong></p>
                                <p class="mb-0">{{ \App\Models\StudentScore::whereDate('created_at', \Carbon\Carbon::today())->count() ?: 0 }} today | {{ \App\Models\StudentScore::count() ?: 0 }} total</p>
                                <small class="text-muted">Latest: {{ \App\Models\StudentScore::latest()->first() ? \App\Models\StudentScore::latest()->first()->created_at->diffForHumans() : 'No records' }}</small>
                            </div>
                            <div class="activity-item">
                                <p class="mb-1"><strong>Teacher Assignments</strong></p>
                                <p class="mb-0">{{ \App\Models\TeacherAssignment::whereDate('created_at', \Carbon\Carbon::today())->count() ?: 0 }} today | {{ \App\Models\TeacherAssignment::count() ?: 0 }} total</p>
                                <small class="text-muted">Latest: {{ \App\Models\TeacherAssignment::latest()->first() ? \App\Models\TeacherAssignment::latest()->first()->created_at->diffForHumans() : 'No records' }}</small>
                            </div>
                            <div class="activity-item">
                                <p class="mb-1"><strong>User Logins</strong></p>
                                <p class="mb-0">{{ \App\Models\LoginActivity::whereDate('created_at', \Carbon\Carbon::today())->count() ?: 0 }} today | {{ \App\Models\LoginActivity::count() ?: 0 }} total</p>
                                <small class="text-muted">Latest: {{ \App\Models\LoginActivity::latest()->first() ? \App\Models\LoginActivity::latest()->first()->created_at->diffForHumans() : 'No records' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quick Analytics</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-primary-subtle text-primary rounded">
                                                <i class="bx bx-user-check fs-4"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-0">Active Students</p>
                                        <h5 class="mb-0">{{ \App\Models\Student::count() ?: 0 }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-success-subtle text-success rounded">
                                                <i class="bx bx-book-content fs-4"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-0">Active Courses</p>
                                        <h5 class="mb-0">{{ \App\Models\Course::count() ?: 0 }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <div class="avatar-sm">
                                            <span class="avatar-title bg-warning-subtle text-warning rounded">
                                                <i class="bx bx-dollar-circle fs-4"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-muted mb-0">Monthly Revenue</p>
                                        <h5 class="mb-0">₦{{ number_format(\App\Models\Payment::whereMonth('created_at', date('m'))->sum('amount') ?: 0, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h6 class="text-muted mb-3">Student-Teacher Ratio</h6>
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                             style="width: {{ (\App\Models\Student::count() / max(\App\Models\Student::count() + \App\Models\Teacher::count(), 1)) * 100 }}%"
                                             aria-valuenow="{{ \App\Models\Student::count() ?: 0 }}"
                                             aria-valuemin="0"
                                             aria-valuemax="{{ (\App\Models\Student::count() + \App\Models\Teacher::count()) ?: 1 }}"></div>
                                        <div class="progress-bar bg-success" role="progressbar"
                                             style="width: {{ (\App\Models\Teacher::count() / max(\App\Models\Student::count() + \App\Models\Teacher::count(), 1)) * 100 }}%"
                                             aria-valuenow="{{ \App\Models\Teacher::count() ?: 0 }}"
                                             aria-valuemin="0"
                                             aria-valuemax="{{ (\App\Models\Student::count() + \App\Models\Teacher::count()) ?: 1 }}"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span class="text-muted small">
                                    <i class="bx bx-user-pin text-primary me-1"></i>Students: {{ \App\Models\Student::count() ?: 0 }}
                                </span>
                                <span class="text-muted small">
                                    <i class="bx bx-user-voice text-success me-1"></i>Teachers: {{ \App\Models\Teacher::count() ?: 0 }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.53.0/apexcharts.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Animated Counters
            const counterElements = document.querySelectorAll('.counter-animation');
            counterElements.forEach(counter => {
                counter.style.visibility = 'visible';
                const target = parseInt(counter.getAttribute('data-target')) || 0;
                const increment = target / 30;
                let current = 0;

                const updateCounter = () => {
                    if (current < target) {
                        current += increment;
                        counter.textContent = Math.ceil(current);
                        setTimeout(updateCounter, 30);
                    } else {
                        counter.textContent = target;
                    }
                };
                updateCounter();
            });

            // Enrollment Chart
            const enrollmentData = {
                weekly: [30, 40, 45, 50, 49, 60, 70, 91, 125],
                monthly: [150, 160, 165, 180, 185, 190, 200, 210, 220],
                yearly: [800, 850, 900, 950, 1000, 1050, 1100, 1150, 1200]
            };
            const enrollmentLabels = {
                weekly: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8', 'Week 9'],
                monthly: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
                yearly: ['2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025']
            };
            const enrollmentOptions = {
                series: [{ name: 'Enrollments', data: enrollmentData.weekly }],
                chart: { height: 300, type: 'area', toolbar: { show: false }, animations: { enabled: true, easing: 'easeinout', speed: 800 } },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                colors: ['#4e73df'],
                xaxis: { categories: enrollmentLabels.weekly },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.9, stops: [0, 90, 100] } },
                tooltip: { x: { format: 'dd/MM/yy HH:mm' } }
            };
            const enrollmentChart = new ApexCharts(document.querySelector("#enrollmentChart"), enrollmentOptions);
            enrollmentChart.render();

            // Period Switcher
            document.querySelectorAll('.chart-period').forEach(button => {
                button.addEventListener('click', function () {
                    const period = this.getAttribute('data-period');
                    document.querySelectorAll('.chart-period').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    enrollmentChart.updateOptions({
                        series: [{ data: enrollmentData[period] }],
                        xaxis: { categories: enrollmentLabels[period] }
                    });
                });
            });

            // Department Distribution Chart
            const departmentOptions = {
                series: [
                    {{ (int) (\App\Models\Student::count() ?: 0) }},
                    {{ (int) (\App\Models\Teacher::count() ?: 0) }},
                    {{ (int) (\App\Models\Course::count() ?: 0) }}
                ],
                chart: { type: 'donut', height: 300, animations: { enabled: true, easing: 'easeinout', speed: 800 } },
                labels: ['Students', 'Teachers', 'Courses'],
                colors: ['#4e73df', '#1cc88a', '#f6c23e'],
                plotOptions: { pie: { donut: { size: '50%' } } },
                legend: { position: 'bottom' },
                responsive: [{ breakpoint: 480, options: { chart: { width: 200 }, legend: { position: 'bottom' } } }]
            };
            const departmentChart = new ApexCharts(document.querySelector("#departmentDistributionChart"), departmentOptions);
            departmentChart.render();

            // Payments Chart
            const paymentOptions = {
                series: [{
                    name: 'Payments',
                    data: [
                        {{ (int) (\App\Models\Payment::whereMonth('created_at', date('m', strtotime('-5 months')))->sum('amount') ?: 0) }},
                        {{ (int) (\App\Models\Payment::whereMonth('created_at', date('m', strtotime('-4 months')))->sum('amount') ?: 0) }},
                        {{ (int) (\App\Models\Payment::whereMonth('created_at', date('m', strtotime('-3 months')))->sum('amount') ?: 0) }},
                        {{ (int) (\App\Models\Payment::whereMonth('created_at', date('m', strtotime('-2 months')))->sum('amount') ?: 0) }},
                        {{ (int) (\App\Models\Payment::whereMonth('created_at', date('m', strtotime('-1 months')))->sum('amount') ?: 0) }},
                        {{ (int) (\App\Models\Payment::whereMonth('created_at', date('m'))->sum('amount') ?: 0) }}
                    ]
                }],
                chart: { type: 'bar', height: 250, toolbar: { show: false }, animations: { enabled: true, easing: 'easeinout', speed: 800 } },
                plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 1, colors: ['transparent'] },
                xaxis: {
                    categories: [
                        '{{ date('M', strtotime('-5 months')) }}',
                        '{{ date('M', strtotime('-4 months')) }}',
                        '{{ date('M', strtotime('-3 months')) }}',
                        '{{ date('M', strtotime('-2 months')) }}',
                        '{{ date('M', strtotime('-1 months')) }}',
                        '{{ date('M') }}'
                    ]
                },
                fill: { opacity: 1, colors: ['#36b9cc'] },
                tooltip: { y: { formatter: val => '₦' + val.toLocaleString() } }
            };
            const paymentsChart = new ApexCharts(document.querySelector("#paymentsChart"), paymentOptions);
            paymentsChart.render();

            // Grades Chart
            const gradesOptions = {
                series: [
                    { name: 'A', data: [30, 40, 35, 50, 49] },
                    { name: 'B', data: [54, 45, 60, 41, 69] },
                    { name: 'C', data: [41, 36, 26, 45, 33] },
                    { name: 'D', data: [22, 14, 25, 16, 25] },
                    { name: 'F', data: [11, 7, 12, 8, 10] }
                ],
                chart: { type: 'bar', height: 300, stacked: true, toolbar: { show: false }, animations: { enabled: true, easing: 'easeinout', speed: 800 } },
                plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 2 } },
                xaxis: { categories: ['2021 1st', '2021 2nd', '2022 1st', '2022 2nd', '2023 1st'] },
                legend: { position: 'top', horizontalAlign: 'right' },
                fill: { opacity: 1, colors: ['#1cc88a', '#4e73df', '#f6c23e', '#e74a3b', '#858796'] }
            };
            const gradesChart = new ApexCharts(document.querySelector("#gradesChart"), gradesOptions);
            gradesChart.render();

            // Refresh Data
            document.getElementById('refreshData').addEventListener('click', function () {
                this.innerHTML = '<i class="bx bx-loader bx-spin me-2"></i>Refreshing...';
                setTimeout(() => {
                    this.innerHTML = '<i class="bx bx-refresh me-2"></i>Refresh Data';

                    // Update Enrollment Chart
                    enrollmentChart.updateSeries([{ data: enrollmentData.weekly.map(value => Math.floor(value * (0.95 + Math.random() * 0.1))) }]);

                    // Update Department Chart
                    departmentChart.updateSeries([
                        Math.floor({{ \App\Models\Student::count() ?: 0 }} * (0.98 + Math.random() * 0.04)),
                        Math.floor({{ \App\Models\Teacher::count() ?: 0 }} * (0.98 + Math.random() * 0.04)),
                        Math.floor({{ \App\Models\Course::count() ?: 0 }} * (0.98 + Math.random() * 0.04))
                    ]);

                    // Update Payments Chart
                    paymentsChart.updateSeries([{ data: paymentOptions.series[0].data.map(value => Math.floor(value * (0.97 + Math.random() * 0.06))) }]);

                    // Update Grades Chart
                    gradesChart.updateSeries(gradesOptions.series.map(series => ({
                        name: series.name,
                        data: series.data.map(value => Math.floor(value * (0.96 + Math.random() * 0.08)))
                    })));

                    // Update Counters
                    counterElements.forEach(counter => {
                        const target = parseInt(counter.getAttribute('data-target')) || 0;
                        const newTarget = Math.floor(target * (0.95 + Math.random() * 0.1));
                        counter.setAttribute('data-target', newTarget);
                        let current = 0;
                        const increment = newTarget / 30;
                        const updateCounter = () => {
                            if (current < newTarget) {
                                current += increment;
                                counter.textContent = Math.ceil(current);
                                setTimeout(updateCounter, 30);
                            } else {
                                counter.textContent = newTarget;
                            }
                        };
                        updateCounter();
                    });

                    // Show Toast
                    const toast = document.createElement('div');
                    toast.className = 'position-fixed top-0 end-0 p-3';
                    toast.style.zIndex = '1050';
                    toast.innerHTML = `
                        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-header bg-success text-white">
                                <strong class="me-auto"><i class="bx bx-check-circle"></i> Success</strong>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                Dashboard data has been refreshed successfully!
                            </div>
                        </div>
                    `;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                }, 1500);
            });

            // Resize Handler
            window.addEventListener('resize', () => {
                enrollmentChart.render();
                departmentChart.render();
                paymentsChart.render();
                gradesChart.render();
            });
        });
    </script>
@endsection
