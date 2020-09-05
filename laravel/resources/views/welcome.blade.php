<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gizmo - Admin</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .position-ref {
            position: relative;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
            margin: 4px;
            padding: 0;
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

    </style>
</head>
<body style="background-color: #FFFCF5;">
<div class="d-flex justify-content-center align-items-center position-ref full-height" style="height: 100vh;">
    <div class="content">
        <div class="title" style="margin-top: -100px;">
            <img src="{{asset('images/logo-blue.svg')}}" alt="Gizmo" height="85px;">
        </div>
        <div class="d-flex flex-column">
            <h1>Health Numeracy Project</h1>
            <p class="lead">Master Statistics - the easy way</p>
            <p>
                @if (Auth::guest())
                    <a class="btn btn-dark btn-lg" href="{{ url('/login') }}" role="button">
                        Login
                    </a>
                @else
                    <a class="btn btn-dark btn-lg" href="{{ url('/home') }}" role="button">
                        Show Dashboard
                    </a>
                @endif
            </p>
        </div>
    </div>
</div>
</body>
</html>

