<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webinar Registration Confirmation</title>
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
        background-color: #f9f9f9; /* Keeping header background same as before */
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
    h1, h2 {
        color: #fb8500; /* Making all heading tags orange */
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
    .webinar-details {
        background-color: #f9f9f9;
        border-left: 4px solid #fb8500;
        padding: 15px;
        margin: 20px 0;
    }
    .webinar-link {
        margin: 25px 0;
        padding: 15px;
        background: #f0f7ff;
        border: 1px solid #cce5ff;
        border-radius: 4px;
        text-align: center;
    }
    .webinar-link a {
        color: #1155CC;
        font-weight: bold;
        word-break: break-all;
    }
    .calendar-links {
        margin: 20px 0;
        text-align: center;
    }
    .calendar-links a {
        display: inline-block;
        margin: 0 5px;
        text-decoration: none;
        color: #fb8500;
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
                <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="Innovstem Logo" class="logo">
            </div>
        </div>
        
        <div class="content">
            <h1>You're Registered for the Webinar!</h1>
            
            <p>Dear <bold>{{ $user->name }},</bold></p>
            
            <p>Thank you for registering for our upcoming webinar <strong>{{ $webinar->title }}</strong>. We're thrilled to have you join us!</p>
            
            <div class="webinar-details">
                <h2>Webinar Details:</h2>
                <p><strong>Title</strong> {{ $webinar->title }}</p>
                <p><strong>Date:</strong> {{ $webinar->webinar_date_time->format('l, F j, Y') }}</p>
                <p><strong>Time:</strong> {{ $webinar->webinar_date_time->format('g:i A') }} IST</p>
            </div>
            
            <div class="webinar-link">
                <p><strong>Your Webinar Link:</strong></p>
                <a href="{{ $webinar->webinar_link }}" target="_blank">{{ $webinar->webinar_link }}</a>
                <p><small>Click the link above to join the webinar at the scheduled time.</small></p>
            </div>
            
            
            <p>We recommend joining a few minutes early to ensure your audio and video are working properly.</p>
            
            <p>If you have any questions before the webinar, please contact us at <a href="mailto:support@innovstem.com">support@innovstem.com</a>.</p>
            
            <p>We look forward to seeing you there!</p>
            
            <p>Best regards,<br>The Innovstem Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Innovstem. All rights reserved.</p>
            <p>123 Education Street, Knowledge City, ST 12345</p>
            
            <div class="social-links">
                <a href="https://facebook.com/innovstem">Facebook</a> |
                <a href="https://twitter.com/innovstem">Twitter</a> |
                <a href="https://linkedin.com/company/innovstem">LinkedIn</a> |
                <a href="https://instagram.com/innovstem">Instagram</a>
            </div>
            
            <p>
                <small>You received this email because you registered for a webinar on Innovstem. 
            </p>
        </div>
    </div>
</body>
</html>