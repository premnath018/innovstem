<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Status Notification</title>
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
        .appointment-details {
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
        .status-success {
            color: #28a745;
            font-weight: bold;
        }
        .status-failed {
            color: #dc3545;
            font-weight: bold;
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
            <h1>Appointment Status Update</h1>
            
            <p>Dear {{ $appointment->name }},</p>
            
            <p>Your appointment booking has been processed. Please find the status and details below:</p>
            
            <div class="appointment-details">
                <p><strong>Status:</strong> 
                    <span class="{{ $status === 'Paid' ? 'status-success' : 'status-failed' }}">{{ ucfirst($status) }}</span>
                </p>
                <p><strong>Acknowledgment Number:</strong> {{ $appointment->ack ?? 'N/A' }}</p>
                <p><strong>Name:</strong> {{ $appointment->name }}</p>
                <p><strong>Email:</strong> {{ $appointment->email }}</p>
                <p><strong>Mobile Number:</strong> {{ $appointment->mobile_number }}</p>
                <p><strong>User Type:</strong> {{ $appointment->user_type }}</p>
                <p><strong>Stauts:</strong> {{ $appointment->appointment_status }}</p>
                <p><strong>Package:</strong> {{ $appointment->package->package_name }}</p>
                <p><strong>Slot:</strong> {{ \Carbon\Carbon::parse($appointment->slot->slot_date)->format('F j, Y') }} at {{ \Carbon\Carbon::parse($appointment->slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($appointment->slot->end_time)->format('h:i A') }}</p>
                @if($status === 'Paid')
                    <p><strong>Transaction ID:</strong> {{ $appointment->transaction_id ?? 'N/A' }}</p>
                    <p><strong>Amount Paid:</strong> ₹{{ number_format($appointment->amount_paid ?? 0, 2) }}</p>
                @else
                    <p><strong>Reason:</strong> Payment processing failed. Please try again or contact support.</p>
                @endif
            </div>
            
            @if($status === 'Paid')
                <div style="text-align: center;">
                    <a href="https://innovstem.com/" class="btn">View Appointment</a>
                </div>
                <p>We look forward to assisting you at your scheduled appointment. If you have any questions, please contact our support team.</p>
            @else
                <div style="text-align: center;">
                    <a href="https://innovstem.com/" class="btn">Try Again</a>
                </div>
                <p>Please attempt to book again or contact our support team for assistance.</p>
            @endif
            
            <p>Best regards,<br>The Innovstem Team</p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Innovstem. All rights reserved.</p>
            <p>Powering Youngminds</p>
            
            <div class="social-links">
                <a href="https://instagram.com/innovstem">Instagram</a>
            </div>
            
            <p><small>This is an automated message, please do not reply to this email.</small></p>
        </div>
    </div>
</body>
</html>