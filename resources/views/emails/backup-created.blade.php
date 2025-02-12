<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($type) }} Backup Notification</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f0f9f6;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #20b2aa, #3cb371);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 30px;
            color: #2c3e50;
        }

        .success-icon {
            background: #e7f7f3;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-icon svg {
            width: 40px;
            height: 40px;
            fill: #2ed573;
        }

        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            border-top: 1px solid #eaeaea;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2ed573;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>

        <div class="content">
            <div class="success-icon">
                <svg viewBox="0 0 20 20">
                    <path
                        d="M10,0 C4.5,0 0,4.5 0,10 C0,15.5 4.5,20 10,20 C15.5,20 20,15.5 20,10 C20,4.5 15.5,0 10,0 Z M8,15 L3,10 L4.4,8.6 L8,12.2 L15.6,4.6 L17,6 L8,15 Z" />
                </svg>
            </div>

            <h2 style="text-align: center; color: #20b2aa;">{{ ucfirst($type) }} Backup Created</h2>

            <p>Your {{ strtolower($type) }} backup has been created successfully. The backup file is attached to this
                email for your records.</p>

            <p>Backup Details:</p>
            <ul style="color: #666;">
                <li>Type: {{ ucfirst($type) }}</li>
                <li>Date: {{ now()->format('F j, Y') }}</li>
                <li>Time: {{ now()->format('g:i A') }}</li>
            </ul>
        </div>

        <div class="footer">
            <p>Regards,<br>{{ config('app.name') }}</p>
        </div>
    </div>
</body>

</html>
