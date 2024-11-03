@extends('student.layouts.receipt_layout')

@section('title', 'Payment Receipt')

@section('receipt')
    <div class="tm_container">
        <div class="tm_pos_invoice_wrap" id="tm_download_section">
            <div class="tm_pos_invoice_top">
                <div class="tm_pos_company_logo">
                    <img src="{{ asset('logo.png') }}" alt="logo" style="width:45px; height:45px" class="logo">
                </div>
                <div class="tm_pos_qrcode">
                    {!! QrCode::size(100)->generate(route('receipts.show', $receipt->id)) !!}
                </div>
                <div class="tm_pos_company_name">{{ config('app.name') }}</div>
                <div class="tm_pos_company_address">{{ config('app.address') }}</div>
                <div class="tm_pos_company_mobile">Email: {{ config('app.email') }}</div>
            </div>
            <div class="tm_pos_invoice_body">
                <div class="tm_pos_invoice_heading"><span>Payment Receipt</span></div>
                <ul class="tm_list tm_style1">
                    <li>
                        <div class="tm_list_title">Name:</div> <br>
                        <div class="tm_list_desc">{{ $receipt->payment->student->user->full_name }}</div>
                    </li>
                    <li class="text-right">
                        <div class="tm_list_title">Receipt No:</div> <br>
                        <div class="tm_list_desc">{{ $receipt->receipt_number }}</div>
                    </li>
                    <li>
                        <div class="tm_list_title">Student Id:</div> <br>
                        <div class="tm_list_desc">{{ $receipt->payment->student->matric_number }}</div>
                    </li>
                    <li class="text-right">
                        <div class="tm_list_title">Date:</div> <br>
                        <div class="tm_list_desc">{{ $receipt->date->format('d.m.Y') }}</div>
                    </li>
                </ul>
                <table class="tm_pos_invoice_table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $receipt->payment->paymentType->name }}</td>
                            <td>{{ number_format($receipt->amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="tm_bill_list">
                    <div class="tm_bill_list_in">
                        <div class="tm_bill_title tm_bill_focus">Total Amount:</div>
                        <div class="tm_bill_value tm_bill_focus">{{ number_format($receipt->amount, 2) }}</div>
                    </div>
                </div>
                <div class="tm_pos_sample_text">Thank you for your payment. This receipt is proof of payment for the
                    specified service.</div>
                <div class="tm_pos_invoice_footer">Powered by {{ config('app.name') }}</div>
            </div>
        </div>
        <div class="tm_invoice_btns tm_hide_print">
            <a href="javascript:window.print()" class="tm_invoice_btn tm_color1">
                <span class="tm_btn_icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                        <path
                            d="M384 368h24a40.12 40.12 0 0040-40V168a40.12 40.12 0 00-40-40H104a40.12 40.12 0 00-40 40v160a40.12 40.12 0 0040 40h24"
                            fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                        <rect x="128" y="240" width="256" height="208" rx="24.32" ry="24.32" fill="none"
                            stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
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

            <a href="{{route('parent.view.dashboard')}}" class="tm_invoice_btn tm_color1">
                <span class="tm_btn_icon">
                    <i class="fas fa-home"></i>
                </span>
                <span class="tm_btn_text">Home</span>
            </a>
        </div>
    </div>
@endsection
