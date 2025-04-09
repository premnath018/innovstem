<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Received</title>
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
            background-color: #f9f9f9;
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
            color: #fb8500;
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
            color: #1155CC;
            text-decoration: none;
            font-weight: bold;
        }
        .application-details {
            margin: 25px 0;
            padding: 15px;
            background: #f0f7ff;
            border: 1px solid #cce5ff;
            border-radius: 4px;
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
            <h1>Application Received</h1>
            
            <p>Dear {{ $application->applicant_name }},</p>
            
            <p>Thank you for applying to Innovstem! We have received your application and it will be processed soon. Below are the details of your submission:</p>
            
            <div class="application-details">
                <p><strong>Job Title:</strong> {{ $career->title }}</p>
                <p><strong>Applicant Name:</strong> {{ $application->applicant_name }}</p>
                <p><strong>Email:</strong> {{ $application->email }}</p>
                <p><strong>Phone:</strong> {{ $application->phone ?? 'Not provided' }}</p>
                <p><strong>Status:</strong> {{ $application->status }}</p>
            </div>
            
            <p>We’ll review your application and get back to you as soon as possible. If you have any questions, feel free to contact us at <a href="mailto:careers@innovstem.com">careers@innovstem.com</a>.</p>
            
            <p>Best regards,<br>The Innovstem Recruitment Team</p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Innovstem. All rights reserved.</p>
            <p>123 Education Street, Knowledge City, ST 12345</p>
            
            <div class="social-links">
                <a href="https://facebook.com/innovstem">Facebook</a> |
                <a href="https://twitter.com/innovstem">Twitter</a> |
                <a href="https://linkedin.com/company/innovstem">LinkedIn</a> |
                <a href="https://instagram.com/innovstem">Instagram</a>
            </div>
            
            <p><small>This is an automated message, please do not reply to this email.</small></p>
        </div>
    </div>
</body>
</html>