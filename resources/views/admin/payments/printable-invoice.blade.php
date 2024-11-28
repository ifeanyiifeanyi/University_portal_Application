<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Invoice - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .invoice {
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: 0;
            width: 55%;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .header h1 {
            color: #333;
            margin: 0;
        }
        .invoice-title {
            font-size: 24px;
            color: #444;
            margin-bottom: 20px;
            text-align: center;
        }
        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .invoice-details p {
            margin: 10px 0;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 14px;
            color: #777;
        }
        @media print {
            body {
                background-color: white;
            }
            .invoice {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice">
        <img src="{{ asset('nursinglogo.webp') }}" alt="{{ config('app.name') }} Logo" class="watermark">
        <div class="header">
            <img src="{{ asset('nursinglogo.webp') }}" alt="{{ config('app.name') }} Logo">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <h2 class="invoice-title">Payment Invoice</h2>
        <div class="invoice-details">
            <div>
                <p><strong>Student Name:</strong> {{ $student->user->full_name }}</p>
                <p><strong>Matric Number:</strong> {{ $student->matric_number }}</p>
                <p><strong>Department:</strong> {{ $student->department->name }}</p>
                <p><strong>Level:</strong> {{ $student->current_level }}</p>
                <p><strong>Payment Status:</strong> PENDING</p>
            </div>
            <div>
                <p><strong>Payment Type:</strong> {{ $paymentType->name }}</p>
                <p><strong>Academic Session:</strong> {{ $academicSession->name }}</p>
                <p><strong>Semester:</strong> {{ $semester->name }}</p>
                <p><strong>Date:</strong> {{ date('F d, Y') }}</p>
            </div>
        </div>
        <div class="total">
            <p><strong>Total Amount:</strong> ₦{{ number_format($paymentType->amount, 2) }}</p>
        </div>
        <div class="footer">
            <p>Please present this invoice when making your payment.</p>
            <p>This is an official document of {{ config('app.name') }}.</p>
        </div>
    </div>
    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
