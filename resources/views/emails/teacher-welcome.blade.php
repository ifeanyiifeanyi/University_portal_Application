<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to Our Teaching Platform</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
        }
        .header {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }
        .content { padding: 20px; }
        .footer {
            text-align: center;
            font-size: 0.8em;
            color: #777;
            margin-top: 20px;
        }
        .login-btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Your Teaching Platform Account</h2>
        </div>
        <div class="content">
            <p>Hello {{ $teacherName }},</p>

            <p>Your account has been successfully created. Please find your login credentials below:</p>

            <p><strong>Email: </strong> {{ $email }}</p>
            <p><strong>Temporary Password: </strong> <var>password</var></p>

            <p>For security reasons, please change your password after your first login.</p>

            <a href="{{ config('app.url') }}" class="login-btn">Login to Dashboard</a>

            <p>If you have any questions or need assistance, please contact our support team.</p>

            <p>Best regards,<br>Administrative Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
