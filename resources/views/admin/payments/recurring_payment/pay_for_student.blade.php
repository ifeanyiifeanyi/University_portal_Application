@extends('admin.layouts.admin')

@section('title', 'Create Recurring Payment')

@section('admin')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Create Recurring Payment Subscription</h5>
            </div>
            <div class="card-body">
                <form id="recurringPaymentForm" action="{{ route('admin.recurring-payments.store') }}" method="POST">
                    @csrf

                    <!-- Department & Level Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="department_id">Department</label>
                                <select name="department_id" id="department_id"
                                    class="form-control @error('department_id') is-invalid @enderror" required>
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="level">Level</label>
                                <select name="level" id="level"
                                    class="form-control @error('level') is-invalid @enderror" required disabled>
                                    <option value="">Select Level</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Student Selection -->
                    <div id="studentSection" class="mb-4" style="display: none;">
                        <h6>Select Student</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Active Subscriptions</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsList"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div id="paymentSection" class="mb-4" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plan_id">Payment Plan</label>
                                    <select name="plan_id" id="plan_id"
                                        class="form-control @error('plan_id') is-invalid @enderror" required>
                                        <option value="">Select Payment Plan</option>
                                        @foreach ($plans as $plan)
                                            <option value="{{ $plan->id }}" data-amount="{{ $plan->amount }}">
                                                {{ $plan->name }} (₦{{ number_format($plan->amount) }}/month)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('plan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="number_of_payments">Number of Payments</label>
                                    <input type="number" name="number_of_payments" id="number_of_payments"
                                        class="form-control @error('number_of_payments') is-invalid @enderror"
                                        min="1" max="12" required>
                                    @error('number_of_payments')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div id="summarySection" class="mb-4" style="display: none;">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Payment Summary</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p>Monthly Amount: ₦<span id="monthlyAmount">0.00</span></p>
                                        <p>Number of Payments: <span id="paymentCount">0</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p>Total Amount: ₦<span id="totalAmount">0.00</span></p>
                                        <p>Start Date: <span id="startDate"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div id="paymentMethodSection" class="mb-4" style="display: none;">
                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <select name="payment_method" id="payment_method"
                                class="form-control @error('payment_method') is-invalid @enderror" required>
                                <option value="">Select Payment Method</option>
                                <option value="online">Online Payment</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash">Cash Payment</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none;">
                        Process Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('recurringPaymentForm');
            const departmentSelect = document.getElementById('department_id');
            const levelSelect = document.getElementById('level');

            function formatMoney(amount) {
                return new Intl.NumberFormat('en-NG', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(amount);
            }

            // Format percentage
            function formatPercentage(value) {
                return new Intl.NumberFormat('en-NG', {
                    minimumFractionDigits: 1,
                    maximumFractionDigits: 1
                }).format(value) + '%';
            }

            // Initialize date display
            document.getElementById('startDate').textContent = new Date().toLocaleDateString();

            // Handle department change
            departmentSelect.addEventListener('change', async function() {
                const departmentId = this.value;
                levelSelect.disabled = !departmentId;

                if (departmentId) {
                    try {
                        const response = await fetch(`/admin/get-department-levels/${departmentId}`);
                        const department = await response.json();

                        // Clear and populate level select
                        levelSelect.innerHTML = '<option value="">Select Level</option>';

                        const levels = getDepartmentLevels(department);
                        levels.forEach(level => {
                            const option = new Option(level, level);
                            levelSelect.appendChild(option);
                        });

                        // Reset subsequent sections
                        resetFormSections();
                    } catch (error) {
                        console.error('Error fetching department levels:', error);
                    }
                }
            });

            // Handle level change
            levelSelect.addEventListener('change', async function() {
                if (this.value && departmentSelect.value) {
                    await loadStudents(departmentSelect.value, this.value);
                }
            });

            // Handle payment plan and month count changes
            document.getElementById('plan_id').addEventListener('change', calculatePayment);
            document.getElementById('month_count').addEventListener('change', calculatePayment);

            // Helper Functions
            function getDepartmentLevels(department) {
                switch (department.level_format) {
                    case 'nd_hnd':
                        return ['ND1', 'ND2', 'HND1', 'HND2'];
                    case 'nursing':
                        return ['RN1', 'RN2', 'RN3'];
                    case 'midwifery':
                        return ['RMW1', 'RMW2', 'RMW3'];
                    default:
                        return Array.from({
                            length: department.duration
                        }, (_, i) => (i + 1) * 100);
                }
            }

            async function loadStudents(departmentId, level) {
                try {
                    const response = await fetch(
                        `/admin/payments/students?department_id=${departmentId}&level=${level}`);
                    const students = await response.json();

                    const tbody = document.getElementById('studentsList');
                    tbody.innerHTML = students.map(student => `
                <tr>
                    <td><input type="radio" name="student_id" value="${student.id}" required></td>
                    <td>${student.student_id}</td>
                    <td>${student.name}</td>
                    <td>${formatSubscriptions(student.recurring_subscriptions)}</td>
                    <td>₦${formatNumber(student.balance || 0)}</td>
                </tr>
            `).join('');

                    // Show student section and payment section
                    document.getElementById('studentSection').style.display = 'block';
                    document.getElementById('paymentSection').style.display = 'block';
                } catch (error) {
                    console.error('Error loading students:', error);
                }
            }

            // Render students table with subscription information
            function renderStudents(students) {
                const tbody = document.getElementById('studentsList');
                tbody.innerHTML = students.map(student => {
                    const activeSubscriptions = student.recurring_subscriptions
                        .map(sub => `
                    <div class="mb-2">
                        <strong>${sub.plan_name}</strong><br>
                        Balance: ₦${formatMoney(sub.balance)}<br>
                        Status: <span class="badge ${getStatusBadgeClass(sub.status)}">${sub.status}</span><br>
                        Progress: ${formatPercentage(sub.percentage_paid)}
                    </div>
                `).join('') || 'No active subscriptions';

                    return `
                <tr>
                    <td><input type="radio" name="student_id" value="${student.id}" required></td>
                    <td>${student.student_id}</td>
                    <td>${student.name}</td>
                    <td>${activeSubscriptions}</td>
                </tr>
            `;
                }).join('');
            }

            // Get appropriate badge class based on status
            function getStatusBadgeClass(status) {
                switch (status.toLowerCase()) {
                    case 'paid':
                        return 'bg-success';
                    case 'pending':
                        return 'bg-warning';
                    case 'inactive':
                        return 'bg-danger';
                    default:
                        return 'bg-primary';
                }
            }

            // async function calculatePayment() {
            //     const planId = document.getElementById('plan_id').value;
            //     const monthCount = document.getElementById('month_count').value;

            //     if (planId && monthCount) {
            //         try {
            //             const response = await fetch(
            //                 `/admin/payments/calculate?plan_id=${planId}&month_count=${monthCount}`);
            //             const data = await response.json();

            //             document.getElementById('monthlyAmount').textContent = formatNumber(data
            //                 .monthly_amount);
            //             document.getElementById('totalAmount').textContent = formatNumber(data.total_amount);
            //             document.getElementById('duration').textContent = data.duration_months;

            //             // Show summary and payment method sections
            //             document.getElementById('summarySection').style.display = 'block';
            //             document.getElementById('paymentMethodSection').style.display = 'block';
            //             document.getElementById('submitBtn').style.display = 'block';
            //         } catch (error) {
            //             console.error('Error calculating payment:', error);
            //         }
            //     }
            // }
            async function calculatePayment() {
                const planId = document.getElementById('plan_id').value;
                const numberOfPayments = document.getElementById('number_of_payments').value;

                if (planId && numberOfPayments) {
                    try {
                        const response = await fetch(
                            `/admin/payments/calculate?plan_id=${planId}&number_of_payments=${numberOfPayments}`
                        );
                        const data = await response.json();

                        document.getElementById('monthlyAmount').textContent = formatMoney(data
                            .amount_per_month);
                        document.getElementById('totalAmount').textContent = formatMoney(data.total_amount);
                        document.getElementById('paymentCount').textContent = data.number_of_payments;
                        document.getElementById('startDate').textContent = new Date(data.start_date)
                            .toLocaleDateString();

                        // Show payment sections
                        ['summarySection', 'paymentMethodSection', 'submitBtn'].forEach(id => {
                            document.getElementById(id).style.display = 'block';
                        });
                    } catch (error) {
                        console.error('Error calculating payment:', error);
                    }
                }
            }

            function formatNumber(number) {
                return new Intl.NumberFormat().format(number);
            }

            function formatSubscriptions(subscriptions) {
                if (!subscriptions || !subscriptions.length) return 'None';
                return subscriptions.map(sub =>
                    `${sub.recurring_payment_plan.name} (₦${formatNumber(sub.balance)})`
                ).join('<br>');
            }

            function resetFormSections() {
                ['studentSection', 'paymentSection', 'summarySection',
                    'paymentMethodSection', 'submitBtn'
                ].forEach(id => {
                    document.getElementById(id).style.display = 'none';
                });
            }
        });
    </script> --}}



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('recurringPaymentForm');
            const departmentSelect = document.getElementById('department_id');
            const levelSelect = document.getElementById('level');

            function formatMoney(amount) {
                return new Intl.NumberFormat('en-NG', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(amount);
            }

            // Format percentage
            function formatPercentage(value) {
                return new Intl.NumberFormat('en-NG', {
                    minimumFractionDigits: 1,
                    maximumFractionDigits: 1
                }).format(value) + '%';
            }

            // Initialize date display
            document.getElementById('startDate').textContent = new Date().toLocaleDateString();

            // Handle department change
            departmentSelect.addEventListener('change', async function() {
                const departmentId = this.value;
                levelSelect.disabled = !departmentId;

                if (departmentId) {
                    try {
                        // First get the department to access its level_format
                        const deptResponse = await fetch(`/admin/departments/${departmentId}`);
                        const department = await deptResponse.json();

                        // Then get the levels
                        const levelsResponse = await fetch(
                            `/admin/get-department-levels/${departmentId}`);
                        const displayLevels = await levelsResponse.json();
                     

                        // Clear and populate level select
                        levelSelect.innerHTML = '<option value="">Select Level</option>';

                        // Map the display levels to numeric values
                        displayLevels.forEach((displayLevel) => {
                            let numericValue;

                            // Convert display level to numeric level
                            switch (department.level_format) {
                                case 'nd_hnd':
                                    if (displayLevel.startsWith('ND')) {
                                        numericValue = parseInt(displayLevel.replace('ND',
                                            '')) * 100;
                                    } else {
                                        numericValue = (parseInt(displayLevel.replace('HND',
                                            '')) + 2) * 100;
                                    }
                                    break;
                                case 'nursing':
                                case 'midwifery':
                                    numericValue = parseInt(displayLevel.slice(-1)) * 100;
                                    break;
                                default:
                                    numericValue = displayLevel;
                            }

                            const option = new Option(displayLevel, numericValue);
                            levelSelect.appendChild(option);
                        });

                        // Reset subsequent sections
                        resetFormSections();
                    } catch (error) {
                        console.error('Error fetching department data:', error);
                    }
                }
            });

            // Handle level change
            levelSelect.addEventListener('change', async function() {
                if (this.value && departmentSelect.value) {
                    await loadStudents(departmentSelect.value, this.value);
                }
            });

            // Handle payment plan and payment count changes
            document.getElementById('plan_id').addEventListener('change', calculatePayment);
            document.getElementById('number_of_payments').addEventListener('change', calculatePayment);

            async function loadStudents(departmentId, level) {
                try {
                    const response = await fetch(
                        `/admin/payments/students?department_id=${departmentId}&level=${level}`);
                    const students = await response.json();
                    console.log(students);

                    // Render students with detailed subscription info
                    renderStudents(students);

                    // Show student section and payment section
                    document.getElementById('studentSection').style.display = 'block';
                    document.getElementById('paymentSection').style.display = 'block';
                } catch (error) {
                    console.error('Error loading students:', error);
                }
            }

            // Render students table with subscription information
            // Render students table based on the service method output
            function renderStudents(students) {
                const tbody = document.getElementById('studentsList');

                // If no students found
                if (!students || students.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="4" class="text-center">No students found for this level</td></tr>`;
                    return;
                }

                tbody.innerHTML = students.map(student => {
                    // You need to fetch student name from related user model
                    // First try to access through user relationship if loaded
                    let studentName = 'Unknown';
                    if (student.user) {
                        studentName = student.user.first_name + ' ' + student.user.last_name + ' ' + student.user.other_name;
                    }

                    // Handle recurring subscriptions display
                    const activeSubscriptions = student.recurring_subscriptions?.length > 0 ?
                        student.recurring_subscriptions.map(sub => `
                <div class="mb-2">
                    <strong>${sub.plan_name || 'Subscription Plan'}</strong><br>
                    Balance: ₦${formatMoney(sub.balance || 0)}<br>
                    Status: <span class="badge ${getStatusBadgeClass(sub.status)}">${sub.status || 'Unknown'}</span><br>
                    Progress: ${formatPercentage(sub.percentage_paid || 0)}
                </div>
            `).join('') :
                        'No active subscriptions';

                    return `
            <tr>
                <td><input type="radio" name="student_id" value="${student.id}" required></td>
                <td>${student.matric_number || 'N/A'}</td>
                <td>${studentName}</td>
                <td>${activeSubscriptions}</td>
            </tr>
        `;
                }).join('');
            }

            // Get appropriate badge class based on status
            function getStatusBadgeClass(status) {
                switch (status?.toLowerCase()) {
                    case 'paid':
                        return 'bg-success';
                    case 'pending':
                        return 'bg-warning';
                    case 'inactive':
                        return 'bg-danger';
                    default:
                        return 'bg-primary';
                }
            }

            async function calculatePayment() {
                const planId = document.getElementById('plan_id').value;
                const numberOfPayments = document.getElementById('number_of_payments').value;

                if (planId && numberOfPayments) {
                    try {
                        const response = await fetch(
                            `/admin/payments/calculate?plan_id=${planId}&number_of_payments=${numberOfPayments}`
                        );
                        const data = await response.json();
                        console.log(data);

                        document.getElementById('monthlyAmount').textContent = formatMoney(data
                            .amount_per_month);
                        document.getElementById('totalAmount').textContent = formatMoney(data.total_amount);
                        document.getElementById('paymentCount').textContent = data.number_of_payments;
                        document.getElementById('startDate').textContent = new Date(data.start_date)
                            .toLocaleDateString();

                        // Show payment sections
                        ['summarySection', 'paymentMethodSection', 'submitBtn'].forEach(id => {
                            document.getElementById(id).style.display = 'block';
                        });
                    } catch (error) {
                        console.error('Error calculating payment:', error);
                    }
                }
            }

            function formatNumber(number) {
                return new Intl.NumberFormat().format(number);
            }

            function resetFormSections() {
                ['studentSection', 'paymentSection', 'summarySection',
                    'paymentMethodSection', 'submitBtn'
                ].forEach(id => {
                    document.getElementById(id).style.display = 'none';
                });
            }
        });
    </script>
@endsection
