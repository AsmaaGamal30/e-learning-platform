<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>OTP Code</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 20px;">
        <h2 style="color: #333;">Hello {{ $name }},</h2>

        <p style="font-size: 16px; color: #555;">
            Use the following One-Time Password (OTP) to log in to your account:
        </p>

        <div style="text-align: center; margin: 20px 0;">
            <span style="display: inline-block; background: #007bff; color: #fff; font-size: 24px; letter-spacing: 4px; padding: 10px 20px; border-radius: 6px;">
                {{ $otp }}
            </span>
        </div>

        <p style="font-size: 14px; color: #777;">
            This code will expire in 10 minutes. Please do not share it with anyone.
        </p>

        <p style="font-size: 14px; color: #777;">
            If you did not request this, you can safely ignore this email.
        </p>

        <p style="margin-top: 30px; font-size: 14px; color: #555;">
            Thanks,<br>
            <strong>{{ config('app.name') }}</strong> Team
        </p>
    </div>
</body>
</html>
