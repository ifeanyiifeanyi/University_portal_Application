<!-- resources/views/emails/ticket-response.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Ticket Response</title>
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }

        /* Base styles */
        body {
            background-color: #f8fafc;
            color: #2d3748;
            height: 100%;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }

        .wrapper {
            background-color: #f8fafc;
            margin: 0;
            padding: 50px;
            width: 100%;
        }

        .inner-body {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin: 0 auto;
            padding: 0;
            width: 570px;
        }

        /* Header section */
        .header {
            padding: 25px;
            text-align: center;
            background: #1a56db;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }

        /* Content section */
        .content {
            background: #ffffff;
            padding: 35px;
        }

        .ticket-info {
            background: #f3f4f6;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .ticket-info p {
            margin: 5px 0;
            color: #4a5568;
        }

        .response {
            background: #ffffff;
            padding: 16px;
            border-left: 4px solid #1a56db;
            margin-top: 20px;
        }

        /* Footer section */
        .footer {
            margin: 0 auto;
            padding: 20px;
            text-align: center;
            width: 570px;
        }

        .footer p {
            color: #6b7280;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="inner-body">
            <div class="header">
                <h1>Support Ticket Response</h1>
            </div>

            <div class="content">
                <div class="ticket-info">
                    <p><strong>Ticket #:</strong> {{ $ticketNumber }}</p>
                    <p><strong>Subject:</strong> {{ $subject }}</p>
                    <p><strong>Department:</strong> {{ $department }}</p>
                </div>

                <div class="response">
                    {!! nl2br(e($response)) !!}
                </div>

                <div style="margin-top: 30px;">
                    <p><strong>Best regards,</strong></p>
                    <p>{{ $respondentName }}</p>
                    <p>{{ $department }} Department</p>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>This is an automated response to your support ticket. Please do not reply to this email directly.</p>
            <p>If you need to provide additional information, please log in to your support portal.</p>
        </div>
    </div>
</body>

</html>
