<!-- resources/views/admin/recurring_payments/pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Student Payments for {{ $year }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 20px;
            font-size: 11px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            margin: 2px;
            background-color: #28a745;
            color: white;
            border-radius: 3px;
            font-size: 9px;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .text-muted {
            color: #666;
            font-size: 9px;
        }

        .no-records {
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Student Payments for {{ $year }}</h2>
        <p>Generated on {{ now()->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>SN</th>
                <th>Student Name</th>
                <th>Level</th>
                <th>Amount Paid</th>
                <th>Months Paid</th>
                <th>Payment Date</th>
                <th>Covered Period</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $payment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ $payment['student_name'] }} <br>
                        <span class="text-muted">{{ $payment['email'] }}, {{ $payment['phone_number'] }}</span>
                    </td>
                    <td>{{ $payment['student_level'] }}</td>
                    <td>₦{{ number_format($payment['total_amount'], 2) }}</td>
                    <td>
                        {{ $payment['number_of_months'] }} month{{ $payment['number_of_months'] > 1 ? 's' : '' }}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($payment['payment_date'])->format('d M Y') }}
                    </td>
                    <td>
                        @if($payment['number_of_months'] > 0)
                            @foreach($payment['months_list'] as $month)
                                <span class="badge">
                                    {{ $month['name'] }} {{ $month['year'] }}
                                </span>
                            @endforeach
                        @else
                            <span class="badge badge-danger">No months paid</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="no-records">
                        No payments found for {{ $year }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px; text-align: right;">
        <p>Total Amount: ₦{{ number_format($yearlyTotals->where('year', $year)->first()['total'] ?? 0, 2) }}</p>
    </div>
</body>
</html>
