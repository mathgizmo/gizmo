<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gizmo - Admin')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />
    <script type="text/javascript" async src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-MML-AM_CHTML"></script>
    {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700"> --}}
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('styles')
</head>

<body id="app-layout" class="sb-nav-fixed">
@include('partials.header')
<div id="layoutSidenav">
    @include('partials.sidebar')
    <div id="layoutSidenav_content">
        <main>
            <ol class="breadcrumb m-0" style="padding: 2px 8px;">
                <li class="breadcrumb-item"><a href="{{ route('home')  }}">Main Menu</a></li>
                @yield('breadcrumb')
            </ol>
            <div class="container-fluid py-2">
                @yield('content')
            </div>
        </main>
        @include('partials.footer')
    </div>
</div>
<script src="{{ asset('js/font-awesome.min.js') }}"></script>
{{-- <script src="{{ asset('js/jquery.min.js') }}"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var repMode = $("#reply_mode option:selected").val();
        setTF(repMode);
        $("#reply_mode").on('change', function () {
            setTF($(this).val());
        });
        function setTF(repMode) {
            if (repMode == "TF") {
                $(".answer-TF").each(function () {
                    $(this).val($(this).prev('input').val());
                    $(this).prev('input').val($(this).val());
                });
                $(".answer-input").hide();
                $(".answer-TF").show();
            } else {
                $(".answer-TF").hide();
                $(".answer-input").show();
            }
        }
        $(".answer-TF").on('change', function () {
            var selection = $(this).val();
            $(this).prev('input').val(selection);
        });

        $('#level_id').on('change', function () {
            $value = $(this).val();
            $.ajax({
                type: 'get',
                url: '{{URL:: to('/questions/create')}}',
                data: {'level_id': $value},
                success: function (data) {
                    $('#unit_id').html(data);
                }
            });
        });
        $('#unit_id').on('change', function () {
            $value = $(this).val();
            $.ajax({
                type: 'get',
                url: '{{URL:: to('/questions/create')}}',
                data: {'unit_id': $value},
                success: function (data) {
                    $('#topic_id').html(data);
                }
            });
        });
        $('#topic_id').on('change', function () {
            $value = $(this).val();
            $.ajax({
                type: 'get',
                url: '{{URL:: to('/questions/create')}}',
                data: {'topic_id': $value},
                success: function (data) {
                    $('#lesson_id').html(data);
                }
            });
        });
    });
</script>
@yield('scripts')
</body>
</html>
