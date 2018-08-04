<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gizmo</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">
    <script type="text/javascript" async src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-MML-AM_CHTML"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link href="{{ URL::asset('css/app.css') }}" rel="stylesheet">

    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }
    </style>
</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    GiZmo
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                @if(auth()->check())
                    <ul class="nav navbar-nav">
                        <li><a href="{{ Route('question_views.create') }}">Create Question</a></li>
                        <li><a href="{{ url('/question_views') }}">Manage Questions</a></li>
                        <li><a href="{{ url('/level_views') }}">Manage Levels</a></li>
                        <li><a href="{{ url('/unit_views') }}">Manage Units</a></li>
                        <li><a href="{{ url('/topic_views') }}">Manage Topics</a></li>
                        <li><a href="{{ url('/lesson_views') }}">Manage Lessons</a></li>
                        <li><a href="{{ url('/placement_views') }}">Manage Placements</a></li>
                        @if(auth()->user()->is_admin)
                            <li><a href="{{ route('users.index') }}">Administrators</a></li>
                        @endif
                        <li><a href="{{ route('students.index') }}">Participants</a></li>
                        @if(auth()->user()->is_admin)
                            <li><a href="{{ route('settings.index') }}">Settings</a></li>
                        @endif
                        <li><a href="{{ route('error_report.index', 'new') }}">Error Report</a></li>
                    </ul>
                @endif

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- JavaScripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}

    <script type="text/javascript">
    $(document).ready(function () {

        var repMode = $("#reply_mode option:selected").val();
        setTF(repMode);

        $("#reply_mode").on('change', function(){
            setTF($(this).val());

        });
        function setTF(repMode) {
            if(repMode == "TF"){
                $(".answer-TF").each(function(){
                    $(this).val($(this).prev('input').val());
                    $(this).prev('input').val($(this).val());
                });
                $(".answer-input").hide();
                $(".answer-TF").show();
            }
            else {
                $(".answer-TF").hide();
                $(".answer-input").show();
            }
        }
        $(".answer-TF").on('change', function(){
            var selection = $(this).val();
            $(this).prev('input').val(selection);
        });


    });

    </script>

        <script type="text/javascript">
        $('#level_id').on('change', function(){
            $value=$(this).val();
            $.ajax({
                type : 'get',
                url  : '{{URL:: to('/question_views/create')}}',
                data : {'level_id':$value},
                success: function(data){
                    $('#unit_id').html(data);
                    }
                });
            });
        $('#unit_id').on('change', function(){
            $value=$(this).val();
            $.ajax({
                type : 'get',
                url  : '{{URL:: to('/question_views/create')}}',
                data : {'unit_id':$value},
                success: function(data){
                    //alert(data);
                    $('#topic_id').html(data);
                    }
                });
            });

    $('#topic_id').on('change', function(){
        $value=$(this).val();
        $.ajax({
            type : 'get',
            url  : '{{URL:: to('/question_views/create')}}',
            data : {'topic_id':$value},
            success: function(data){
                //alert(data);
                $('#lesson_id').html(data);
            }
        });
    });

    $(function () {

        $("#addImageModal button#save-image").on('click', function() {
            $('#addImageModal').modal('hide');
            $('form#topic label#change-image img').removeClass();

            $('form#topic label#add-image').hide();
            $('form#topic label#change-image').show();

            var intVal = $('#addImageModal input[type=checkbox]:checked').val();
            $('form#topic label#change-image img').addClass(intVal);
            $('form#topic input[name=image_id').val(intVal);
        });

        $( "#addImageModal input[type=checkbox]" ).on( "click", function() {
            $('#addImageModal input[type=checkbox]').prop('checked', false);
            $(this).prop('checked', true);
        });

    });
    </script>

    @yield('scripts')

</body>
</html>
