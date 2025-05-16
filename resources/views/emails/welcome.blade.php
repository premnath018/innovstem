<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Innovstem!</title>
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
        .hero {
            background-color: #fb8500;
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .hero h1 {
            color: white;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 28px;
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
        h2 {
            color: #fb8500;
            font-size: 18px;
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
        .feature-box {
            margin: 25px 0;
            display: flex;
            align-items: flex-start;
        }
        .feature-icon {
            flex: 0 0 50px;
            font-size: 24px;
            color: #fb8500;
            text-align: center;
            padding-top: 5px;
        }
        .feature-content {
            flex: 1;
            padding-left: 15px;
        }
        .feature-content h3 {
            margin-top: 0;
            color: #fb8500;
            font-size: 16px;
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
        
        <div class="hero">
            <h1>Welcome to Innovstem!</h1>
            <p>Your journey into the future of education begins now.</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            
            <p>Thank you for joining Innovstem! We're thrilled to welcome you to our innovative learning platform that's designed to help you achieve your educational goals.</p>
            
        
            <h2>Here's what you can do with your new account:</h2>
            
            <div class="feature-box">
                <div class="feature-icon">📚</div>
                <div class="feature-content">
                    <h3>Access Quality Courses</h3>
                    <p>Browse our extensive library of courses designed by industry experts.</p>
                </div>
            </div>
            
            <div class="feature-box">
                <div class="feature-icon">🎯</div>
                <div class="feature-content">
                    <h3>Track Your Progress</h3>
                    <p>Monitor your learning journey with detailed progress tracking.</p>
                </div>
            </div>
            
            <div class="feature-box">
                <div class="feature-icon">💬</div>
                <div class="feature-content">
                    <h3>Join Our Community</h3>
                    <p>Connect with fellow learners and share your experiences.</p>
                </div>
            </div>
            
            
            <p>If you have any questions or need assistance, our support team is always ready to help at <a href="mailto:info@innovstem.com">info@innovstem.com</a>.</p>
            
            <p>We're excited to be part of your learning journey!</p>
            
            <p>Best regards,<br>The Innovstem Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Innovstem. All rights reserved.</p>
            <p>Powering Youngminds</p>
            
            <div class="social-links">
                <p>Connect with us:</p>
                <a href="https://instagram.com/innovstem">Instagram</a>
            </div>
            
        </div>
    </div>
</body>
</html>