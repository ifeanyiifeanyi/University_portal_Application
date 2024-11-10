<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-header {
            text-align: center;
            padding-bottom: 20px;
        }
        .alert-title {
            color: #2d3748;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .login-details {
            background-color: #f7fafc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .detail-item {
            margin-bottom: 10px;
        }
        .detail-label {
            color: #718096;
            font-size: 14px;
        }
        .detail-value {
            color: #2d3748;
            font-weight: 500;
        }
        .action-steps {
            margin-bottom: 20px;
        }
        .action-step {
            margin-bottom: 8px;
        }
        .button {
            display: inline-block;
            background-color: #4a5568;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #2d3748;
        }
        .footer {
            text-align: center;
            color: #718096;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1 class="alert-title">New Device Login Detected</h1>
        </div>

        <div class="login-details">
            <p>We detected a login to your account from a new device:</p>

            <div class="detail-item">
                <div class="detail-label">Browser</div>
                <div class="detail-value">{{ $activity->browser }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Device</div>
                <div class="detail-value">{{ $activity->device_type }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Operating System</div>
                <div class="detail-value">{{ $activity->operating_system }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Location</div>
                <div class="detail-value">{{ $activity->location }}</div>
            </div>

            <div class="detail-item">
                <div class="detail-label">Time</div>
                <div class="detail-value">{{ $activity->created_at }}</div>
            </div>
        </div>

        <div class="action-steps">
            <p><strong>If this wasn't you, please secure your account immediately by:</strong></p>
            <div class="action-step">1. Logging in to your account</div>
            <div class="action-step">2. Changing your password</div>
            <div class="action-step">3. Enabling two-factor authentication if available</div>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('login.view') }}" class="button">Login to Your Account</a>
        </div>

        <p>If this was you, no further action is needed.</p>

        <div class="footer">
            <p>Thanks,<br>{{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
