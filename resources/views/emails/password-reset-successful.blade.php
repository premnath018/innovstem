<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Successful</title>
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
        background-color: #f9f9f9; /* Keeping header background unchanged */
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
    h1, h3 {
        color: #fb8500; /* Making h1 and h3 orange */
        margin-top: 0;
    }
    .btn {
        display: inline-block;
        background-color: #fb8500;
        color: white;
        text-decoration: none;
        padding: 10px 25px;
        border-radius: 4px;
        margin: 20px 0;
        font-weight: bold;
    }
    a {
        color: #1155CC; /* Making all links orange */
        text-decoration: none;
        font-weight: bold;
    }
    .success-box {
        background-color: #e7f7ed;
        border-left: 4px solid #28a745;
        padding: 15px;
        margin: 20px 0;
    }
    .security-tips {
        background-color: #f9f9f9;
        padding: 15px;
        margin: 20px 0;
        border-radius: 4px;
    }
    .security-tips h3 {
        margin-top: 0;
        color: #fb8500; /* Making security tips heading orange */
    }
    .security-tips ul {
        padding-left: 20px;
    }
    .social-links {
        margin-top: 20px;
    }
    .social-links a {
        display: inline-block;
        margin: 0 8px;
        color: #fb8500;
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
            <h1>Password Reset Successful</h1>
                        
            <div class="success-box">
                <p>Your password has been successfully reset. You can now log in to your account with your new password.</p>
            </div>
            
            <div style="text-align: center;">
                <a href="https://innovstem.com/auth/login" class="btn">Log In Now</a>
            </div>
            
            <div class="security-tips">
                <h3>For Your Security:</h3>
                <ul>
                    <li>Never share your password with anyone</li>
                    <li>Use a unique password for different platforms</li>
                    <li>Consider using a password manager</li>
                </ul>
            </div>
            
            <p>If you did not request this password change, please contact our security team immediately at <a href="mailto:support@innovstem.com">support@innovstem.com</a>.</p>
            
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