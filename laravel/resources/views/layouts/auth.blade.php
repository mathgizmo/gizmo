<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gizmo - Admin')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        @media screen and (max-width: 992px) {
            .logo {
                display: none;
            }
            .card {
                margin-top: 24px !important;
            }
        }
        @media screen and (max-width: 600px) {
            .container, .container .row, .container .row > div {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .card {
                margin: 8px !important;
                max-height: 98vh !important;
            }
        }
    </style>
    @yield('styles')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body style="background-color: #696969;">
<div id="layoutAuthentication">
    <a class="logo" href="{{route('welcome')}}" style="position: absolute; top: 12px; left: 12px;">
        <img class="logo-img" src="{{asset('images/logo-blue.svg')}}" alt="Gizmo" height="60" style="filter: grayscale(100%) brightness(150%);
            -webkit-filter: grayscale(100%) brightness(150%);">
    </a>
    <div id="layoutAuthentication_content">
        <main>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="{{ asset('js/font-awesome.min.js') }}"></script>
@yield('scripts')
</body>
</html>
