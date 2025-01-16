<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background-color: #08db72;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .email-body {
            padding: 20px;
        }

        .email-body p {
            margin: 0 0 15px;
        }

        .email-body .message-content {
            background-color: #f1f1f1;
            border-left: 4px solid #08db72;
            padding: 15px;
            border-radius: 4px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .email-footer {
            background-color: #f8f9fa;
            color: #666;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            border-top: 1px solid #eaeaea;
        }

        .email-footer a {
            color: #004a8f;
            text-decoration: none;
        }

        @media only screen and (max-width: 600px) {
            .email-header h1 {
                font-size: 18px;
            }

            .email-body {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>{{ config('app.name_title', 'College Of Nursing Sciences, Onitsha.') }}</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p>Dear {{ $studentName }},</p>

            <div class="message-content">
                {!! nl2br(e($messageContent)) !!}
            </div>

            <p>Best regards,<br>
            Administration</p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>
                You are receiving this email as a student of <strong>{{ config('app.name') }}</strong>.
                For more information, please contact us at <a href="mailto:info@collegeofnursing.com">info@collegeofnursing.com</a>.
            </p>
        </div>
    </div>
</body>
</html>
