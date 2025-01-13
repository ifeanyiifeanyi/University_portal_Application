@extends('student.layouts.invoice_layout')
@php
    function getStatusColor($status)
    {
        return match ($status) {
            'paid' => 'success',
            'partial' => 'info',
            'pending' => 'warning',
            'processing' => 'primary',
            'failed' => 'danger',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
            'refunded' => 'dark',
            default => 'secondary',
        };
    }
@endphp

<style>
    @media print {
        .no-print {
            display: none !important;
        }
    }

    .payment-method-name {
        display: none;
    }

    @media print {
        .payment-method-name {
            display: block;
        }
    }
</style>
@section('invoice')
<div class="tm_container">
    @include('admin.alert')
    <div class="tm_invoice_wrap">
        <div class="tm_invoice tm_style1" id="tm_download_section">
            <div class="tm_invoice_in">
                <div class="tm_invoice_head tm_mb20">
                    <div class="tm_invoice_left">
                        <div class="tm_logo"><img src="{{ asset('nursinglogo.webp') }}" alt="Logo"></div>
                    </div>
                    <div class="tm_invoice_right tm_text_right tm_mobile_hide">
                        <div class="tm_primary_color tm_f50 tm_text_uppercase">Invoice</div>
                    </div>
                </div>
                <div class="tm_invoice_info tm_mb20">
                    <div class="tm_invoice_seperator tm_gray_bg"></div>
                    <div class="tm_invoice_info_list">
                        <p class="tm_invoice_number tm_m0">Invoice No: <b
                                class="tm_primary_color">{{ Str::upper($invoice->invoice_number) }}</b></p>
                        <p class="tm_invoice_date tm_m0">Date: <b
                                class="tm_primary_color">{{ $invoice->created_at->format('d.m.Y') }}</b></p>
                    </div>

                </div>
                <div class="tm_invoice_head tm_mb10">
                    <div class="tm_invoice_left">
                        <p class="tm_mb2 tm_f16"><b
                                class="tm_primary_color tm_text_uppercase">{{ config('app.name') }}</b></p>
                        <p>
                            {{ config('app.address') }}<br>
                            {{ config('app.email') }}<br>
                            {{ config('app.phone') }}
                        </p>
                    </div>
                    <div class="tm_invoice_right">
                        <div class="tm_grid_row tm_col_3 tm_col_2_sm tm_invoice_table tm_round_border">
                            <div>
                                <p class="tm_m0">Student Name:</p>
                                <b class="tm_primary_color">{{ $invoice->student->user->full_name }}</b>
                            </div>
                            <div>
                                <p class="tm_m0">Student ID:</p>
                                <b class="tm_primary_color">{{ $invoice->student->matric_number }}</b>
                            </div>
                            <div>
                                <p class="tm_m0">Term:</p>
                                <b class="tm_primary_color">{{ $invoice->semester->name }}</b>
                            </div>
                            <div>
                                <p class="tm_m0">Balance Due:</p>
                                <b class="tm_primary_color">₦{{ number_format($invoice->amount, 2) }}</b>
                            </div>
                            <div>
                                <p class="tm_m0">Due Date:</p>
                                <b class="tm_primary_color">
                                    {{ optional($invoice->due_date ?? $invoice->created_at->addDays(7))->format('d F Y') }}
                                </b>
                            </div>
                            <div>
                                <p class="tm_m0">Session:</p>
                                <b class="tm_primary_color">{{ $invoice->academicSession->name }}</b>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="tm_table tm_style1">
                    <div class="tm_round_border">
                        <div class="tm_table_responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="tm_width_5 tm_semi_bold tm_primary_color">Details</th>
                                        <th class="tm_width_5 tm_semi_bold tm_primary_color">Due Date</th>
                                        <th class="tm_width_2 tm_semi_bold tm_primary_color tm_text_right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="tm_gray_bg"><b class="tm_primary_color">

                                            <td class="tm_width_5">{{ $invoice->paymentType->name }}</td>
                                            <td class="tm_width_5">
                                                {{ optional($invoice->due_date ?? $invoice->created_at->addDays(7))->format('d F Y') }}
                                            </td>
                                            <td class="tm_width_2 tm_text_right">
                                                ₦{{ number_format($invoice->amount, 2) }}</td>
                                    </tr>
                                    <!-- Add more rows if you have itemized fees -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tm_invoice_footer">
                        <div class="tm_left_footer">
                            <p class="tm_mb2"><b class="tm_primary_color">Payment info:</b></p>
                            <p class="text-muted">
                                {{ Str::upper(str_replace('_', ' ', $invoice->paymentMethod->config['payment_type'])) }}<br>
                            </p>
                            <p class="text-muted">
                                Amount: ₦{{ number_format($invoice->amount, 2) }}
                            </p>
                            <p>
                                <button class="btn btn-sm btn-{{ getStatusColor($invoice->status) }}">
                                    {{ Str::title($invoice->status) }}</button>
                            </p>

                        </div>
                        <div class="tm_right_footer">
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="tm_width_3 tm_primary_color tm_border_none tm_bold">Subtotal</td>
                                        <td class="tm_width_3 tm_primary_color tm_text_right tm_border_none tm_bold">
                                            ₦{{ number_format($invoice->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="tm_width_3 tm_text_right tm_border_none tm_pt0 tm_ternary_color">
                                            Please note: Additional transfer and processing charges may apply and will be reflected in the final payment amount.
                                        </td>
                                    </tr>
                                    <tr class="tm_border_top">
                                        <td class="tm_width_3 tm_border_top_0 tm_bold tm_f16 tm_primary_color">Grand
                                            Total</td>
                                        <td
                                            class="tm_width_3 tm_border_top_0 tm_bold tm_f16 tm_primary_color tm_text_right">
                                            ₦{{ number_format($invoice->amount, 2) }}</td>
                                    </tr>
                                    <tr class="tm_border_top">
                                        <td
                                            class="tm_width_3 tm_border_top_0 tm_bold tm_f16 tm_primary_color"colspan='2'>

                                            <form action="{{ route('student.fees.processPayment') }}" method="POST"
                                                class="d-inline">
                                                @csrf

                                                <input type="hidden" name="invoice_number"
                                                    value="{{ $invoice->invoice_number }}">

                                                    <input type="hidden" name="is_installment"
                                                    value="{{ $invoice->is_installment }}">


                                                <input type="hidden" name="payment_type_id"
                                                    value="{{ $invoice->payment_type_id }}">


                                                <input type="hidden" name="department_id"
                                                    value="{{ $invoice->department_id }}">

                                                <input type="hidden" name="level" value="{{ $invoice->level }}">

                                                <input type="hidden" name="student_id"
                                                    value="{{ $invoice->student_id }}">

                                                <input type="hidden" name="academic_session_id"
                                                    value="{{ $invoice->academic_session_id }}">

                                                <input type="hidden" name="semester_id"
                                                    value="{{ $invoice->semester_id }}">

                                                <input type="hidden" name="amount" value="{{ $invoice->amount }}">

                                                <input type="hidden" name="payment_method_id"
                                                    value="{{ $invoice->payment_method_id }}">
                                                &nbsp;
                                                <button
                                                    onclick="return confirm('Are you sure you want to proceed with the payment?')"
                                                    type="submit" class="btn btn-sm ml-3 no-print"
                                                    style="background:blueviolet;color:white">
                                                    <i class="fas fa-credit-card mr-2"></i>Pay now
                                                </button>
                                            </form>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-3 tm_text_center tm_m0_md">
                    <p class="tm_m0">This invoice was created on a computer and is valid without the signature and
                        seal.</p>
                </div>
            </div>
        </div>
        <div class="tm_invoice_btns tm_hide_print">
            <a href="javascript:window.print()" class="tm_invoice_btn tm_color1">
                <span class="tm_btn_icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                        <path
                            d="M384 368h24a40.12 40.12 0 0040-40V168a40.12 40.12 0 00-40-40H104a40.12 40.12 0 00-40 40v160a40.12 40.12 0 0040 40h24"
                            fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                        <rect x="128" y="240" width="256" height="208" rx="24.32" ry="24.32"
                            fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                        <path d="M384 128v-24a40.12 40.12 0 00-40-40H168a40.12 40.12 0 00-40 40v24" fill="none"
                            stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                        <circle cx="392" cy="184" r="24" fill='currentColor' />
                    </svg>
                </span>
                <span class="tm_btn_text">Print</span>
            </a>
            <button id="tm_download_btn" class="tm_invoice_btn tm_color2">
                <span class="tm_btn_icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                        <path
                            d="M320 336h76c55 0 100-21.21 100-75.6s-53-73.47-96-75.6C391.11 99.74 329 48 256 48c-69 0-113.44 45.79-128 91.2-60 5.7-112 35.88-112 98.4S70 336 136 336h56M192 400.1l64 63.9 64-63.9M256 224v224.03"
                            fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="32" />
                    </svg>
                </span>
                <span class="tm_btn_text">Download</span>
            </button>
            <a href="javascript:window.history.back()" class="tm_invoice_btn tm_color1">
                <span class="tm_btn_icon">
                    <!-- SVG for Return to Previous Page (Left Arrow) -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                        <path d="M244 400L100 256l144-144M120 256h292" fill="none" stroke="currentColor"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="48" />
                    </svg>
                </span>
                <span class="tm_btn_text">Return To Editing</span>
            </a>

        </div>
    </div>
</div>
@endsection
