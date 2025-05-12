@extends('student.layouts.student')

@section('title', 'Student Pay Recurring Payments')
@section('student')
    <style>
        .payment-container {
            max-width: 650px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .plan-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .amount-preview {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
        }

        .payment-btn {
            background-color: #0066cc;
            border: none;
            padding: 12px 0;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .payment-btn:hover {
            background-color: #0052a3;
        }
    </style>
    <div class="container-xxl mt-3">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Pay Recurring Payments</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                    <li class="breadcrumb-item active">Recurring Payments</li>
                </ol>
            </div>
        </div>

        <div class="container">
            <div class="payment-container">
                <h2 class="mb-4 text-center">Complete Your Subscription</h2>
                @include('messages')
                <div class="plan-details">
                    <h4>{{ $plan->name }}</h4>
                    <p>{{ $plan->description }}</p>
                    <div class="d-flex justify-content-between">
                        <span>Base Rate:</span>
                        <span>₦{{ number_format($plan->amount, 2) }} per month</span>
                    </div>
                </div>





                {{-- <form id="paymentForm" action="{{ route('student.recurring.processpayment') }}" method="POST">
                @csrf
                <input type="hidden" name="recurring_plan_id" value="{{ $plan->id }}">
                <input type="hidden" name="amount_per_month" value="{{ $plan->amount }}">
                <input type="hidden" id="hiddenTotalAmount" name="total_amount" value="{{ $plan->amount }}">
                <input type="hidden" id="selectedMonths" name="selected_months">

                <div class="mb-3">
                    <label class="form-label">Select Months to Pay</label>

                    @php
                        $currentYear = date('Y');
                        $availableMonths = 0;
                        $paidMonths = array_map('strval', $paidMonths ?? []);
                    @endphp

                    @if (count($paidMonths) >= 12)
                        <div class="alert alert-info">
                            You have already paid for all months in {{ $currentYear }}.
                        </div>
                    @else
                        <div class="row">
                            @for ($i = 1; $i <= 12; $i++)
                                @php
                                    $monthName = date('F', mktime(0, 0, 0, $i, 1));
                                    $isPaid = in_array((string)$i, $paidMonths);
                                @endphp

                                @unless ($isPaid)
                                    @php $availableMonths++; @endphp
                                    <div class="col-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input month-checkbox"
                                                   type="checkbox"
                                                   value="{{ $i }}"
                                                   id="month{{ $i }}"
                                                   data-month-name="{{ $monthName }} {{ $currentYear }}">
                                            <label class="form-check-label" for="month{{ $i }}">
                                                {{ $monthName }} {{ $currentYear }}
                                            </label>
                                        </div>
                                    </div>
                                @endunless
                            @endfor
                        </div>
                    @endif
                </div>

                <div class="mb-4 mt-4">
                    <div class="d-flex justify-content-between">
                        <span>Total Amount:</span>
                        <span class="amount-preview" id="totalAmount">₦0.00</span>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg payment-btn"
                            type="submit"
                            @if ($availableMonths === 0) disabled @endif
                            onclick="return confirm('Are you sure you want to proceed with this payment?')">
                        Pay Now
                    </button>
                </div>
            </form> --}}

                <form id="paymentForm" action="{{ route('student.recurring.processpayment') }}" method="POST">
                    @csrf
                    <input type="hidden" name="recurring_plan_id" value="{{ $plan->id }}">
                    <input type="hidden" name="amount_per_month" value="{{ $plan->amount }}">
                    <input type="hidden" id="hiddenTotalAmount" name="total_amount" value="0">
                    <input type="hidden" id="selectedMonths" name="selected_months">

                    <div class="mb-3">
                        <label class="form-label">Select Months to Pay</label>

                        @php
                            $currentYear = date('Y');
                            $availableMonths = 0;
                            $paidMonths = array_map('strval', $paidMonths ?? []);
                        @endphp

                        @if (count($paidMonths) >= 12)
                            <div class="alert alert-info">
                                You have already paid for all months in {{ $currentYear }}.
                            </div>
                        @else
                            <div class="row">
                                @for ($i = 1; $i <= 12; $i++)
                                    @php
                                        $monthName = date('F', mktime(0, 0, 0, $i, 1));
                                        $isPaid = in_array((string) $i, $paidMonths);
                                    @endphp

                                    @unless ($isPaid)
                                        @php $availableMonths++; @endphp
                                        <div class="col-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input month-checkbox" type="checkbox"
                                                    value="{{ $i }}" id="month{{ $i }}"
                                                    data-month-name="{{ $monthName }} {{ $currentYear }}">
                                                <label class="form-check-label" for="month{{ $i }}">
                                                    {{ $monthName }} {{ $currentYear }}
                                                </label>
                                            </div>
                                        </div>
                                    @endunless
                                @endfor
                            </div>
                        @endif
                    </div>

                    <div class="mb-4 mt-4">
                        <div class="d-flex justify-content-between">
                            <span>Base Amount:</span>
                            <span class="amount-preview" id="baseAmount">₦0.00</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Late Fees:</span>
                            <span class="amount-preview text-danger" id="lateFeeTotal">₦0.00</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Total Amount:</strong>
                            <strong class="amount-preview" id="totalAmount">₦0.00</strong>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-lg payment-btn" type="submit"
                            @if ($availableMonths === 0) disabled @endif
                            onclick="return confirm('Are you sure you want to proceed with this payment?')">
                            Pay Now
                        </button>
                    </div>
                </form>







            </div>
        </div>
    </div>

    {{-- <script>
    const monthCheckboxes = document.querySelectorAll('.month-checkbox');
    const totalAmountDisplay = document.getElementById('totalAmount');
    const hiddenTotalAmountInput = document.getElementById('hiddenTotalAmount');
    const selectedMonthsInput = document.getElementById('selectedMonths');
    const amountPerMonth = {{ $plan->amount }};

    function updateSubscriptionDetails() {
        const selectedMonths = Array.from(monthCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => ({
                month: checkbox.value,
                name: checkbox.dataset.monthName
            }));

        // Calculate total amount based on selected months
        const totalAmount = selectedMonths.length * amountPerMonth;

        // Update total amount display
        totalAmountDisplay.textContent = '₦' + new Intl.NumberFormat().format(totalAmount.toFixed(2));
        hiddenTotalAmountInput.value = totalAmount.toFixed(2);

        // Store selected months as JSON string
        selectedMonthsInput.value = JSON.stringify(selectedMonths.map(m => m.month));
    }

    // Add event listeners to checkboxes
    monthCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSubscriptionDetails);
    });
</script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const monthCheckboxes = document.querySelectorAll('.month-checkbox');
            const baseAmountDisplay = document.getElementById('baseAmount');
            const lateFeeDisplay = document.getElementById('lateFeeTotal');
            const totalAmountDisplay = document.getElementById('totalAmount');
            const hiddenTotalAmountInput = document.getElementById('hiddenTotalAmount');
            const selectedMonthsInput = document.getElementById('selectedMonths');
            const paymentForm = document.getElementById('paymentForm');
            const planId = document.querySelector('input[name="recurring_plan_id"]').value;

            function updateSubscriptionDetails() {
                // Collect selected months
                const selectedMonths = Array.from(monthCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);

                // If no months selected, reset displays
                if (selectedMonths.length === 0) {
                    baseAmountDisplay.textContent = '₦0.00';
                    lateFeeDisplay.textContent = '₦0.00';
                    totalAmountDisplay.textContent = '₦0.00';
                    hiddenTotalAmountInput.value = '0.00';
                    selectedMonthsInput.value = '[]';
                    return;
                }

                // Call backend to calculate payment details
                fetch('{{ route('student.recurring.calculate.payment') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            plan_id: planId,
                            selected_months: selectedMonths
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Update displays
                        baseAmountDisplay.textContent = '₦' + new Intl.NumberFormat().format(data.base_amount
                            .toFixed(2));
                        lateFeeDisplay.textContent = '₦' + new Intl.NumberFormat().format(data.total_late_fees
                            .toFixed(2));
                        totalAmountDisplay.textContent = '₦' + new Intl.NumberFormat().format(data.total_amount
                            .toFixed(2));

                        // Update hidden inputs
                        hiddenTotalAmountInput.value = data.total_amount.toFixed(2);
                        selectedMonthsInput.value = JSON.stringify(selectedMonths);

                        // Optionally, update month checkboxes with late fee information
                        monthCheckboxes.forEach(checkbox => {
                            const monthLabel = checkbox.closest('.form-check').querySelector(
                                '.form-check-label');
                            const monthNum = checkbox.value;

                            // Remove existing late fee span if exists
                            const existingLateFeeSpan = monthLabel.querySelector('.text-danger');
                            if (existingLateFeeSpan) {
                                existingLateFeeSpan.remove();
                            }

                            // Add late fee span if applicable
                            if (data.late_fees[monthNum] > 0) {
                                const lateFeeSpan = document.createElement('span');
                                lateFeeSpan.classList.add('text-danger');
                                lateFeeSpan.textContent =
                                    ` (Late Fee: ₦${new Intl.NumberFormat().format(data.late_fees[monthNum])})`;
                                monthLabel.appendChild(lateFeeSpan);
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error calculating payment:', error);
                        alert('Unable to calculate payment. Please try again.');
                    });
            }

            // Add event listeners to checkboxes
            monthCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSubscriptionDetails);
            });
        });
    </script>
@endsection
