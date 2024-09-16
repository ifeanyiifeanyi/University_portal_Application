<!-- resources/views/payments/printable-ticket.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .ticket { border: 1px solid #000; padding: 20px; max-width: 600px; margin: 0 auto; }
        .ticket h1 { text-align: center; }
        .ticket-info p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="ticket">
        <h1>Payment Invoice</h1>
        <div class="ticket-info">
            <p><strong>Student Name:</strong> {{ $student->user->full_name }}</p>
            <p><strong>Matric Number:</strong> {{ $student->matric_number }}</p>
            <p><strong>Department:</strong> {{ $student->department->name }}</p>
            <p><strong>Level:</strong> {{ $student->current_level }}</p>
            <p><strong>Payment Type:</strong> {{ $paymentType->name }}</p>
            <p><strong>Amount:</strong> ₦{{ number_format($paymentType->amount, 2) }}</p>
            <p><strong>Academic Session:</strong> {{ $academicSession->name }}</p>
            <p><strong>Semester:</strong> {{ $semester->name }}</p>
        </div>
        <p>Please present this invoice when making your payment.</p>
    </div>
    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
