{{-- resources/views/emails/password-recovery.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Password Recovery</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>Password Recovery Request</h2>

    <p>You have requested to reset your password. Click the link below to reset your password. This link will expire in 24 hours.</p>

    <p style="margin: 25px 0;">
        <a href="{{ $recoveryLink }}"
           style="background-color: #204939;
                  color: white;
                  padding: 10px 20px;
                  text-decoration: none;
                  border-radius: 5px;">
            Reset Password
        </a>
    </p>

    <p>If you did not request a password reset, no further action is required.</p>

    <p>Thanks,<br>
    {{ config('app.name') }}</p>

    <hr style="margin: 20px 0;">

    <p style="font-size: 12px; color: #666;">
        If you're having trouble clicking the "Reset Password" link, copy and paste this URL into your web browser:<br>
        {{ $recoveryLink }}
    </p>
</body>
</html>
