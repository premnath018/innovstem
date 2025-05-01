<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Registration Confirmation</title>
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
    h2 {
        color: #2c3e50; /* Dark blue heading */
        font-size: 18px;
        margin-bottom: 10px;
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
    .course-details {
        background-color: #f9f9f9; /* Light gray background */
        border-left: 4px solid #fb8500; /* Orange left border */
        padding: 15px;
        margin: 20px 0;
        border-radius: 4px; /* Rounded corners for better design */
    }
    .social-links {
        margin-top: 20px;
        text-align: center;
    }
    .social-links a {
        display: inline-block;
        margin: 0 8px;
        color: #fb8500; /* Orange links */
        text-decoration: none;
        font-weight: bold;
    }
    .social-links a:hover {
        text-decoration: underline;
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
            <h1>Course Registration Successful!</h1>
            
            <p>Dear <bold>{{ $user->name }},</bold></p>
            
            <p>Thank you for registering for <strong>{{ $course_title }}</strong>. We're excited to have you join us on this learning journey!</p>
            
            <p>You can access your course materials by logging into your dashboard.</p>
            
            <div style="text-align: center;">
                <a href="https://innovstem.com/dashboard" class="btn">Access Your Course</a>
            </div>
            
            <p>If you have any questions or need assistance, please don't hesitate to contact our support team at <a href="mailto:support@innovstem.com">support@innovstem.com</a>.</p>
            
            <p>Best regards,<br>The Innovstem Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Innovstem. All rights reserved.</p>
            <p>Powering Youngminds</p>
            
            <div class="social-links">
                <a href="https://instagram.com/innovstem">Instagram</a>
            </div>
            
            <p>
                <small>You received this email because you registered for a course on Innovstem. 
            </p>
        </div>
    </div>
</body>
</html>