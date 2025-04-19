<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - IT & GA Ticketing System</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #f8fafc;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }
        .full-height {
            height: 100vh;
        }
        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }
        .position-ref {
            position: relative;
        }
        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }
        .content {
            text-align: center;
            max-width: 768px;
            padding: 0 20px;
        }
        .title {
            font-size: 54px;
            font-weight: 700;
            color: #333;
        }
        .subtitle {
            font-size: 24px;
            margin-bottom: 30px;
            color: #5c6bc0;
        }
        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }
        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 30px;
        }
        .feature-card {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 10px;
            padding: 20px;
            width: 200px;
            text-align: center;
        }
        .feature-card i {
            color: #5c6bc0;
            font-size: 40px;
            margin-bottom: 10px;
        }
        .feature-card h3 {
            font-size: 18px;
            margin: 10px 0;
        }
        .feature-card p {
            font-size: 14px;
            color: #666;
        }
        .m-b-md {
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            background-color: #5c6bc0;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #3f51b5;
        }
    </style>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <div class="flex-center position-ref full-height">
        @if (Route::has('login'))
            <div class="top-right links">
                @auth
                    <a href="{{ url('/home') }}">Dashboard</a>
                @else
                    <a href="{{ route('login') }}">Login</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}">Register</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="content">
            <div class="title m-b-md">
                Company Ticketing System
            </div>
            <div class="subtitle">
                Submit and track IT & GA support requests effortlessly
            </div>

            <div class="features">
                <div class="feature-card">
                    <i class="fas fa-ticket-alt"></i>
                    <h3>Create Tickets</h3>
                    <p>Submit your IT or GA requests through our easy-to-use ticket system</p>
                </div>

                <div class="feature-card">
                    <i class="fas fa-comments"></i>
                    <h3>Live Chat</h3>
                    <p>Communicate directly with support staff through ticket comments</p>
                </div>

                <div class="feature-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Track Progress</h3>
                    <p>Monitor the status and progress of all your submitted tickets</p>
                </div>
            </div>

            <div class="mt-5">
                @auth
                    <a href="{{ url('/home') }}" class="btn">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn">Login to Get Started</a>
                @endauth
            </div>
        </div>
    </div>
</body>
</html>
