<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our School Portal</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-top: 0;
        }
        p {
            color: #555;
            line-height: 1.5;
        }
        .info-block {
            margin-bottom: 20px;
        }
        .info-block h3 {
            color: #333;
            margin-bottom: 5px;
        }
        .portal-link {
            text-align: center;
            margin-top: 30px;
        }
        .portal-link a {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
        }
        .portal-link a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Our School Portal, {{ $user->first_name }}!</h1>
        <div class="info-block">
            <h3>Your Account Details</h3>
            {{-- <p>Username: {{ $user->username }}</p> --}}
            <p>Email: {{ $user->email }}</p>
            <p>Matric Number: {{ $student->matric_number }}</p>
            <p>Password: password (temporary password, please change it later)</p>
        </div>
        <div class="info-block">
            <h3>Next Steps</h3>
            <p>Please log in to the school portal using the credentials provided above. You can then update your profile, check your academic records, and more.</p>
            <p>If you have any questions or need assistance, feel free to contact our support team at <a href="mailto:admin@stcharlesborromeocon.com">admin@stcharlesborromeocon.com</a>.</p>
        </div>
        <div class="portal-link">
            <h3>Access the Portal</h3>
            <p>You can log in using the link below:</p>
            <a href="https://portal.stcharlesborromeocon.com" target="_blank">Go to School Portal</a>
        </div>
        <p>Best regards,<br><strong>The School Administration</strong></p>
    </div>
</body>
</html>
