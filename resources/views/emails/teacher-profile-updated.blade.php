<!DOCTYPE html>
<html>
<body>
    <h2>Profile Update Notification</h2>
    <p>Dear {{ $teacherName }},</p>

    <p>Your profile has been recently updated by {{ $updatedBy }}. If you did not authorize this update or notice any discrepancies, please contact the administrator immediately.</p>

    <p>Best regards,<br>
    {{ config('app.name') }}</p>
</body>
</html>
