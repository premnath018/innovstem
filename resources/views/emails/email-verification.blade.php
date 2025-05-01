<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
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
        background-color: #f9f9f9; /* Orange header */
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
        color: #fb8500; /* Orange heading */
        margin-top: 0;
    }
    .btn {
        display: inline-block;
        background-color: #fb8500; /* Orange button */
        color: white;
        text-decoration: none;
        padding: 10px 25px;
        border-radius: 4px;
        margin: 20px 0;
        font-weight: bold;
    }
    .verification-link {
        margin: 25px 0;
        padding: 15px;
        background: #f0f7ff; /* Light blue background */
        border: 1px solid #cce5ff;
        border-radius: 4px;
        text-align: center;
    }
    .verification-link a {
        color: #fb8500; /* Orange link */
        font-weight: bold;
        word-break: break-all;
    }
    .note {
        background-color: #fff8e1; /* Light yellow warning box */
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
        color: #fb8500; /* Orange social links */
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
            <h1>Verify Your Email Address</h1>
            
            <p>Dear {{ $user->name }},</p>
            
            <p>Thank you for creating an account with Innovstem. To complete your registration and access all features of our platform, please verify your email address.</p>
            
            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="btn">Verify Email Address</a>
            </div>
            
            <p>If the button above doesn't work, you can copy and paste the following link into your browser:</p>
            
            <div class="verification-link">
                <a href="{{ $verificationUrl }}" target="_blank">{{ $verificationUrl }}</a>
            </div>
            
            <div class="note">
                <p><strong>Note:</strong> This verification link will expire in 24 hours for security reasons.</p>
            </div>
            
            <p>Once verified, you'll have full access to all the features and resources available on our platform.</p>
            
            <p>If you did not create an account with Innovstem, please ignore this email or contact us at <a href="mailto:support@innovstem.com">support@innovstem.com</a>.</p>
            
            <p>Best regards,<br>The Innovstem Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Innovstem. All rights reserved.</p>
            <p>Powering Youngminds</p>
            
            <div class="social-links">
                <a href="https://instagram.com/innovstem">Instagram</a>
            </div>
            
            <p>
                <small>You received this email because this email address was used to create an account on Innovstem.</small>
            </p>
        </div>
    </div>
</body>
</html>