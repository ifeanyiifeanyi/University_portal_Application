<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - {{ $student->user->full_name }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#416439">
    <meta name="apple-mobile-web-app-status-bar-style" content="#416439">
    <meta name="msapplication-navbutton-color" content="#416439">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-title" content="Lab">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="msapplication-TileColor" content="#416439">
    <meta name="msapplication-TileImage" content="{{ asset('nursinglogo.webp') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('nursinglogo.webp') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('nursinglogo.webp') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('nursinglogo.webp') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="mask-icon" href="{{ asset('safari-pinned-tab.svg') }}" color="#416439">
    <meta name="apple-mobile-web-app-title" content="Lab">
    <meta name="application-name" content="Lab">
    <meta name="msapplication-config" content="{{ asset('browserconfig.xml') }}">
    <meta name="theme-color" content="#416439">

    <style>
        :root {
            --primary-color: #416439;
            --primary-dark: #234c22;
            --secondary-color: #f8f9fa;
            --text-primary: #2c3e50;
            --text-secondary: #6c757d;
            --border-color: #e9ecef;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-primary);
            min-height: 100vh;
        }

        .student-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: 20px 20px 0 0;
            color: white;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(83, 205, 29, 0.3);
        }

        .student-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            object-fit: cover;
            margin-bottom: 1rem;
        }

        .student-name {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .student-matric {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 500;
        }

        .info-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 1rem 1.5rem;
            border: none;
            font-weight: 600;
        }

        .card-header-custom i {
            margin-right: 0.5rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.3s ease;
        }

        .info-row:hover {
            background-color: #f8f9fa;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
        }

        .info-label i {
            margin-right: 0.5rem;
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }

        .info-value {
            color: var(--text-secondary);
            text-align: right;
            font-weight: 500;
        }

        .payment-item {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .payment-item:hover {
            transform: translateX(5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }

        .payment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .payment-type {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .payment-status {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
        }

        .payment-amount {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--success-color);
            margin-bottom: 0.5rem;
        }

        .payment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .payment-detail {
            display: flex;
            flex-direction: column;
        }

        .payment-detail-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .payment-detail-value {
            font-size: 0.9rem;
            color: var(--text-primary);
            font-weight: 500;
            margin-top: 0.2rem;
        }

        .no-payments {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
        }

        .no-payments i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .current-session-badge {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .student-header {
                padding: 1.5rem;
                border-radius: 15px 15px 0 0;
            }

            .student-avatar {
                width: 100px;
                height: 100px;
            }

            .student-name {
                font-size: 1.5rem;
            }

            .payment-details {
                grid-template-columns: 1fr;
            }

            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .info-value {
                text-align: left;
            }

            .payment-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }

        .fade-in {
            animation: fadeIn 0.8s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-in {
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Subscription Styles */
        .subscription-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .subscription-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .subscription-plan-name {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .subscription-progress {
            min-width: 120px;
        }

        .subscription-stat {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .stat-value {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.2rem;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .months-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .month-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
        }

        .month-badge.paid {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .month-badge.unpaid {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .payment-history-list {
            max-height: 200px;
            overflow-y: auto;
        }

        .payment-history-item {
            padding: 0.8rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            border-left: 3px solid var(--primary-color);
        }

        .subscription-dates {
            border-top: 1px solid #e9ecef;
            padding-top: 1rem;
        }

        @media (max-width: 768px) {
            .subscription-stat {
                margin-bottom: 0.5rem;
            }

            .months-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }

            .subscription-header {
                text-align: center;
            }

            .subscription-progress {
                min-width: auto;
                width: 100%;
                margin-top: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Student Header Card -->
                <div class="info-card fade-in">
                    <div class="student-header">
                        <img src="{{ $student->user->profile_image }}" alt="Student Photo" class="student-avatar">
                        <div class="student-name">{{ $student->user->full_name ?? 'N/A' }}</div>
                        <div class="student-matric">{{ $student->matric_number ?? 'N/A' }}</div>
                        <div class="mt-2">
                            <span
                                class="badge bg-light text-dark fs-6">{{ $student->department->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="accordion" id="studentDetailsAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingPersonalInfo">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapsePersonalInfo" aria-expanded="true"
                                aria-controls="collapsePersonalInfo">
                                <i class="fas fa-user me-2"></i>Personal & Contact Information
                            </button>
                        </h2>
                        <div id="collapsePersonalInfo" class="accordion-collapse collapse show"
                            aria-labelledby="headingPersonalInfo" data-bs-parent="#studentDetailsAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <!-- Personal Information -->
                                    <div class="col-lg-6">
                                        <div class="info-card slide-in">
                                            <div class="card-header-custom">
                                                <i class="fas fa-user"></i>Personal Information
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-graduation-cap"></i>Level
                                                    </div>
                                                    <div class="info-value">
                                                        {{ $student->department->getDisplayLevel($student->current_level) }}
                                                    </div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-id-card"></i>JAMB Number
                                                    </div>
                                                    <div class="info-value">
                                                        {{ $student->jamb_registration_number ?? 'N/A' }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-calendar-alt"></i>Admission Year
                                                    </div>
                                                    <div class="info-value">{{ $student->year_of_admission ?? 'N/A' }}
                                                    </div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-door-open"></i>Entry Mode
                                                    </div>
                                                    <div class="info-value">{{ $student->mode_of_entry ?? 'N/A' }}
                                                    </div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-tint"></i>Blood Group
                                                    </div>
                                                    <div class="info-value">{{ $student->blood_group ?? 'N/A' }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-birthday-cake"></i>Date of Birth
                                                    </div>
                                                    <div class="info-value">
                                                        {{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contact Information -->
                                    <div class="col-lg-6">
                                        <div class="info-card slide-in">
                                            <div class="card-header-custom">
                                                <i class="fas fa-address-book"></i>Contact Information
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-phone"></i>Phone
                                                    </div>
                                                    <div class="info-value">{{ $student->user->phone ?? 'N/A' }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-envelope"></i>Email
                                                    </div>
                                                    <div class="info-value">{{ $student->user->email ?? 'N/A' }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-home"></i>Residential Address
                                                    </div>
                                                    <div class="info-value">
                                                        {{ $student->residential_address ?? 'N/A' }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-map-marker-alt"></i>Permanent Address
                                                    </div>
                                                    <div class="info-value">{{ $student->permanent_address ?? 'N/A' }}
                                                    </div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label">
                                                        <i class="fas fa-user-friends"></i>Guardian Contact
                                                    </div>
                                                    <div class="info-value">{{ $student->next_of_kin_phone ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Session Payment Status -->
                    @php
                        $currentSession = \App\Models\AcademicSession::where('is_current', true)->first();
                        $currentSemester = \App\Models\Semester::where('is_current', true)->first();
                        $currentPayments = $student
                            ->payments()
                            ->when($currentSession, function ($query) use ($currentSession) {
                                return $query->where('academic_session_id', $currentSession->id);
                            })
                            ->when($currentSemester, function ($query) use ($currentSemester) {
                                return $query->where('semester_id', $currentSemester->id);
                            })
                            ->with(['paymentType', 'academicSession', 'semester'])
                            ->get();

                        // Get recurring subscriptions (monthly feeding fee)
                        $currentYearSubscriptions = $student
                            ->recurringSubscriptions()
                            ->whereYear('created_at', date('Y'))
                            ->with('plan')
                            ->get();
                    @endphp
                    <!-- 3. Wrap the Current Session Payment Status section -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingCurrentPayment">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseCurrentPayment" aria-expanded="false"
                                aria-controls="collapseCurrentPayment">
                                <i class="fas fa-credit-card me-2"></i>Current Session Payment Status
                            </button>
                        </h2>
                        <div id="collapseCurrentPayment" class="accordion-collapse collapse"
                            aria-labelledby="headingCurrentPayment" data-bs-parent="#studentDetailsAccordion">
                            <div class="accordion-body">
                                <div class="info-card fade-in">
                                    <div class="card-header-custom">
                                        <i class="fas fa-credit-card"></i>Current Session Payment Status
                                    </div>
                                    <div class="card-body">
                                        @if ($currentSession || $currentSemester)
                                            <div class="current-session-badge">
                                                <i class="fas fa-calendar"></i>
                                                {{ $currentSession->name ?? 'No Current Session' }} -
                                                {{ $currentSemester->name ?? 'No Current Semester' }}
                                            </div>
                                        @endif

                                        @if ($currentPayments->isNotEmpty())
                                            <div class="row">
                                                @foreach ($currentPayments as $payment)
                                                    <div class="col-md-6 mb-3">
                                                        <div class="payment-item">
                                                            <div class="payment-header">
                                                                <div class="payment-type">
                                                                    {{ $payment->paymentType->name ?? $payment->payment_type }}
                                                                </div>
                                                                <span
                                                                    class="payment-status status-{{ strtolower($payment->status) }}">
                                                                    {{ ucfirst($payment->status) }}
                                                                </span>
                                                            </div>
                                                            <div class="payment-amount">
                                                                ₦{{ number_format($payment->amount, 2) }}
                                                            </div>
                                                            <div class="payment-details">
                                                                <div class="payment-detail">
                                                                    <span class="payment-detail-label">Payment
                                                                        Date</span>
                                                                    <span
                                                                        class="payment-detail-value">{{ $payment->created_at->format('M d, Y') }}</span>
                                                                </div>
                                                                <div class="payment-detail">
                                                                    <span
                                                                        class="payment-detail-label">Installment</span>
                                                                    <span
                                                                        class="payment-detail-value">{{ $payment->is_installment ? 'Yes' : 'No' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="no-payments">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <h5>No payments found for current session/semester</h5>
                                                <p class="text-muted">No payment records available for the current
                                                    academic session and
                                                    semester.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Subscription Information -->
                    <!-- Monthly Feeding Fee Status -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFeedingFee">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFeedingFee" aria-expanded="false"
                                aria-controls="collapseFeedingFee">
                                <i class="fas fa-utensils me-2"></i>Monthly Feeding Fee Status ({{ date('Y') }})
                            </button>
                        </h2>
                        <div id="collapseFeedingFee" class="accordion-collapse collapse"
                            aria-labelledby="headingFeedingFee" data-bs-parent="#studentDetailsAccordion">
                            <div class="accordion-body">
                                <div class="info-card fade-in">
                                    <div class="card-header-custom">
                                        <i class="fas fa-utensils"></i>Monthly Feeding Fee Status
                                        ({{ date('Y') }})
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $filteredSubscriptions = $currentYearSubscriptions->filter(function (
                                                $subscription,
                                            ) {
                                                return $subscription->amount_paid > 0;
                                            });
                                        @endphp

                                        @if ($filteredSubscriptions->isNotEmpty())
                                            @foreach ($filteredSubscriptions as $subscription)
                                                <div class="subscription-card mb-4">
                                                    <div class="subscription-header">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                            <div>
                                                                <h5 class="subscription-plan-name">
                                                                    {{ $subscription->plan->name ?? 'Monthly Feeding Fee' }}
                                                                </h5>
                                                            </div>
                                                            <div class="text-end">
                                                                <span
                                                                    class="subscription-status status-{{ strtolower($subscription->status) }}">
                                                                    {{ $subscription->status }}
                                                                </span>
                                                                <div class="subscription-progress mt-2">
                                                                    <div class="progress" style="height: 8px;">
                                                                        <div class="progress-bar bg-success"
                                                                            role="progressbar"
                                                                            style="width: {{ min($subscription->percentage_paid, 100) }}%">
                                                                        </div>
                                                                    </div>
                                                                    <small
                                                                        class="text-muted">{{ number_format($subscription->percentage_paid, 1) }}%
                                                                        paid</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="subscription-summary">
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <div class="subscription-stat">
                                                                    <div class="stat-value text-success">
                                                                        ₦{{ number_format($subscription->amount_paid, 2) }}
                                                                    </div>
                                                                    <div class="stat-label">Amount Paid</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="subscription-stat">
                                                                    <div class="stat-value text-info">
                                                                        {{ $subscription->number_of_payments ?? 0 }}
                                                                    </div>
                                                                    <div class="stat-label">Months Paid</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @php
                                                        $paymentDetails = $subscription->calculatePaidMonthsFromSelected();
                                                    @endphp

                                                    @if ($subscription->amount_paid > 0)
                                                        <div class="months-paid-section">
                                                            <h6 class="mb-3"><i class="fas fa-calendar-check"></i>
                                                                Months Paid For:
                                                            </h6>
                                                            <div class="months-grid">
                                                                @foreach ($paymentDetails['months'] as $month)
                                                                    <div class="month-badge paid">
                                                                        <i class="fas fa-check-circle"></i>
                                                                        {{ $month['name'] }} {{ $month['year'] }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="no-payments">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            <h6>No Months Paid</h6>
                                                            <p class="text-muted">No months have been fully paid for
                                                                this subscription.
                                                            </p>
                                                        </div>
                                                    @endif

                                                    @if ($subscription->payment_history_array && count($subscription->payment_history_array) > 0)
                                                        <div class="payment-history-section mt-4">
                                                            <h6 class="mb-3"><i class="fas fa-history"></i> Payment
                                                                History:</h6>
                                                            <div class="payment-history-list">
                                                                @foreach ($subscription->payment_history_array as $payment)
                                                                    <div class="payment-history-item">
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center">
                                                                            <div>
                                                                                <span class="text-muted">on
                                                                                    {{ \Carbon\Carbon::parse($payment['date'])->format('M d, Y') }}</span>
                                                                            </div>
                                                                            @if (isset($payment['reference']))
                                                                                <small class="text-muted">Ref:
                                                                                    {{ $payment['reference'] }}</small>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif


                                                </div>
                                            @endforeach
                                        @else
                                            <div class="no-payments">
                                                <i class="fas fa-utensils"></i>
                                                <h5>No Monthly Feeding Fee Records</h5>
                                                <p class="text-muted">This student has no monthly feeding fee payments
                                                    for
                                                    {{ date('Y') }}.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div> <!-- subscription information ends -->
                            </div>
                        </div>
                    </div>


                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingPaymentHistory">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapsePaymentHistory" aria-expanded="false"
                                aria-controls="collapsePaymentHistory">
                                <i class="fas fa-history me-2"></i>Payment History
                            </button>
                        </h2>
                        <div id="collapsePaymentHistory" class="accordion-collapse collapse"
                            aria-labelledby="headingPaymentHistory" data-bs-parent="#studentDetailsAccordion">
                            <div class="accordion-body">
                                <!-- All Payment History -->
                                <div class="info-card fade-in">
                                    <div class="card-header-custom">
                                        <i class="fas fa-history"></i>Payment History
                                    </div>
                                    <div class="card-body">
                                        @if ($student->payments->isNotEmpty())
                                            @foreach ($student->payments->sortByDesc('created_at') as $payment)
                                                <div class="payment-item">
                                                    <div class="payment-header">
                                                        <div class="payment-type">
                                                            {{ $payment->paymentType->name ?? $payment->payment_type }}
                                                        </div>
                                                        <span
                                                            class="payment-status status-{{ strtolower($payment->status) }}">
                                                            {{ ucfirst($payment->status) }}
                                                        </span>
                                                    </div>
                                                    <div class="payment-amount">
                                                        ₦{{ number_format($payment->amount, 2) }}</div>
                                                    <div class="payment-details">
                                                        <div class="payment-detail">
                                                            <span class="payment-detail-label">Academic Session</span>
                                                            <span
                                                                class="payment-detail-value">{{ $payment->academicSession->name ?? 'N/A' }}</span>
                                                        </div>
                                                        <div class="payment-detail">
                                                            <span class="payment-detail-label">Semester</span>
                                                            <span
                                                                class="payment-detail-value">{{ $payment->semester->name ?? 'N/A' }}</span>
                                                        </div>
                                                        <div class="payment-detail">
                                                            <span class="payment-detail-label">Payment Date</span>
                                                            <span
                                                                class="payment-detail-value">{{ $payment->created_at->format('M d, Y') }}</span>
                                                        </div>
                                                        <div class="payment-detail">
                                                            <span class="payment-detail-label">Installment</span>
                                                            <span
                                                                class="payment-detail-value">{{ $payment->is_installment ? 'Yes' : 'No' }}</span>
                                                        </div>
                                                        @if ($payment->is_installment && $payment->next_installment_date)
                                                            <div class="payment-detail">
                                                                <span class="payment-detail-label">Next Payment
                                                                    Due</span>
                                                                <span
                                                                    class="payment-detail-value">{{ \Carbon\Carbon::parse($payment->next_installment_date)->format('M d, Y') }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="no-payments">
                                                <i class="fas fa-receipt"></i>
                                                <h5>No Payment Records</h5>
                                                <p class="text-muted">This student has no payment records in the
                                                    system.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add smooth scrolling and enhanced interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add staggered animation to payment items
            const paymentItems = document.querySelectorAll('.payment-item');
            paymentItems.forEach((item, index) => {
                item.style.animationDelay = `${index * 0.1}s`;
                item.classList.add('slide-in');
            });

            // Add hover effects for info rows
            const infoRows = document.querySelectorAll('.info-row');
            infoRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(5px)';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });

            // Auto-refresh functionality for real-time updates
            setTimeout(() => {
                location.reload();
            }, 300000); // Refresh every 5 minutes
        });
    </script>
</body>

</html>
