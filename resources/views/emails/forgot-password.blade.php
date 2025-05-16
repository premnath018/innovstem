<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333333;
        margin: 0;
        padding: 0;
        background-color: #f5f5f5;
    }
    .container {
        max-width: 600px;
        margin: 20px auto;
        background-color: #f9f9f9;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }
    .header {
        background-color: #f9f9f9; /* Updated to orange */
        padding: 20px;
        text-align: center;
    }
    .logo-container {
        margin-bottom: 15px;
    }
    .logo {
        max-width: 180px;
        height: auto;
    }
    .content {
        padding: 30px;
    }
    .footer {
        background-color: #f9f9f9;
        padding: 20px;
        text-align: center;
        font-size: 12px;
        color: #666666;
        border-top: 1px solid #eeeeee;
    }
    h1 {
        color: #fb8500; /* Updated to orange */
        margin-top: 0;
    }
    .btn {
        display: inline-block;
        background-color: #fb8500; /* Updated to orange */
        color: white;
        text-decoration: none;
        padding: 10px 25px;
        border-radius: 4px;
        margin: 20px 0;
        font-weight: bold;
    }
    a {
        color: #1155CC; 
        text-decoration: none;
        font-weight: bold;
    }
    .reset-link {
        margin: 25px 0;
        padding: 15px;
        background: #f0f7ff;
        border: 1px solid #cce5ff;
        border-radius: 4px;
        text-align: center;
    }
    .warning {
        background-color: #fff8e1;
        border-left: 4px solid #ffc107;
        padding: 15px;
        margin: 20px 0;
        font-size: 14px;
    }
    .social-links {
        margin-top: 20px;
    }
    .social-links a {
        display: inline-block;
        margin: 0 8px;
        color: #fb8500; /* Updated to orange */
        text-decoration: none;
    }
</style>

</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <!-- Logo placeholder -->
                <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="Innovstem Logo" class="logo">
            </div>
        </div>
        
        <div class="content">
            <h1>Reset Your Password</h1>
            
            <p>Dear {{ $user->name }},</p>
            
            <p>We received a request to reset your password for your Innovstem account. If you didn't make this request, you can safely ignore this email.</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" target="_blank" class="btn">Reset Password</a>
            </div>
            
            <p>If the button above doesn't work, you can copy and paste the following link into your browser:</p>
            
            <div class="reset-link">
                <a href="{{ $resetUrl }}" target="_blank">{{ $resetUrl }}</a>
            </div>
            
            <div class="warning">
                <p><strong>Note:</strong> This password reset link will expire in 60 minutes for security reasons.</p>
            </div>
            
            <p>If you didn't request a password reset, please contact our support team immediately at <a href="mailto:info@innovstem.com">info@innovstem.com</a>.</p>
            
            <p>Best regards,<br>The Innovstem Security Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Innovstem. All rights reserved.</p>
            <p>Powering Youngminds</p>
            
            <div class="social-links">
                <a href="https://instagram.com/innovstem">Instagram</a>
            </div>
            
            <p>
                <small>This is an automated message, please do not reply to this email.</small>
            </p>
        </div>
    </div>
</body>
</html>