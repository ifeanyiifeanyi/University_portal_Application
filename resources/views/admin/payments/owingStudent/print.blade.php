{{-- resources/views/admin/payments/owingStudent/print.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Unpaid Fees Report</title>
    <style>
        @media print {
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f5f5f5;
            }
            .text-right {
                text-align: right;
            }
            .payment-breakdown {
                font-size: 11px;
            }
            .footer {
                margin-top: 20px;
                text-align: center;
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Students With Unpaid Fees</h2>
        <p>
            Session: {{ $currentSession->name }}<br>
            Semester: {{ $currentSemester->name }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Matric Number</th>
                <th>Department</th>
                <th>Level</th>
                <th>Amount Owed</th>
                <th>Payment Types Due</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                @php
                    $debtDetails = $student->debtDetails;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $student->user->full_name }}</td>
                    <td>{{ $student->matric_number }}</td>
                    <td>{{ Str::title($student->department->name) }}</td>
                    <td>{{ $student->current_level }}</td>
                    <td class="text-right">₦{{ number_format($debtDetails['total'], 2) }}</td>
                    <td class="payment-breakdown">
                        @foreach($debtDetails['breakdown'] as $payment)
                            <div>
                                {{ $payment['name'] }} - ₦{{ number_format($payment['amount'], 2) }}
                                @if($payment['due_date'])
                                    (Due: {{ \Carbon\Carbon::parse($payment['due_date'])->format('M d, Y') }})
                                @endif
                            </div>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Printed on {{ now()->format('F d, Y h:i A') }}
    </div>
</body>
</html>
