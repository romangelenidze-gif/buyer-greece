<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        body {
            background-color: #f5f5f7;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1d1d1f;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f5f5f7;
            padding: 40px 0;
        }
        .email-content {
            max-width: 520px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.04);
        }
        .email-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .email-logo {
            font-size: 28px;
            font-weight: 700;
            color: #1d1d1f;
            text-decoration: none;
            letter-spacing: -0.5px;
        }
        .email-body {
            font-size: 15px;
            line-height: 1.6;
            color: #424245;
            margin-bottom: 32px;
        }
        .email-btn {
            display: block;
            text-align: center;
            background-color: #0071e3;
            color: #ffffff !important;
            padding: 14px 24px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            margin: 24px 0;
            transition: background-color 0.2s;
        }
        .email-btn:hover {
            background-color: #0077ed;
        }
        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #86868b;
            margin-top: 32px;
            line-height: 1.5;
        }
        .email-footer a {
            color: #0071e3;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <!-- Шапка с крупным логотипом -->
            <div class="email-header">
                <a href="{{ config('app.url', 'https://exereto.com') }}" class="email-logo">exereto</a>
            </div>

            <!-- Основной контент письма -->
            <div class="email-body">
                {{ $slot }}
            </div>

            <!-- Футер с языковым разделением или копирайтом -->
            <div class="email-footer">
                <p>&copy; {{ date('Y') }} Exereto. All rights reserved.</p>
                <p>
                    <a href="{{ route('home') }}">Website</a> &bull; 
                    <a href="{{ route('help') }}">Support</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>