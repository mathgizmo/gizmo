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

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

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
                <ul class="nav navbar-nav">
                    <li><a href="{{ url('/home') }}">Home</a></li>
					<li><a href="{{ url('/question_views') }}">Manage Questions</a></li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li><a href="{{ url('/register') }}">Register</a></li>
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
		$("#imageShow").hide();
		$("#shapeShow").hide();
		$("#mcq1Show").hide();
		$("#mcq2Show").hide();
		$("#mcq3Show").hide();
		$("#mcq4Show").hide();
		$("#mcq5Show").hide();
		$("#mcq6Show").hide();
		$("#type").on('change',function(){
			var selection = $(this).val();
			switch(selection){
				case "text":
				$("#imageShow").hide()
				$("#shapeShow").hide()
			break;
			case "draw":
				$("#shapeShow").show()
				$("#imageShow").hide()
			break;
			case "image":
				$("#imageShow").show()
				$("#shapeShow").hide()
			break;
			default:
				$("#shapeShow").hide()
				$("#imageShow").hide()

			}
		});
		
		$("#reply_mode").on('change',function(){
			var selection = $(this).val();
			switch(selection){
				case "mcq3":
				$("#mcq1Show").show()
				$("#mcq2Show").show()
				$("#mcq3Show").show()
				$("#mcq4Show").hide();
				$("#mcq5Show").hide();
				$("#mcq6Show").hide();
			break;
			case "mcq4":
				$("#mcq1Show").show()
				$("#mcq2Show").show()
				$("#mcq3Show").show()
				$("#mcq4Show").show()
				$("#mcq5Show").hide();
				$("#mcq6Show").hide();
				
			break;
			case "mcq5":
				$("#mcq1Show").show()
				$("#mcq2Show").show()
				$("#mcq3Show").show()
				$("#mcq4Show").show()
				$("#mcq5Show").show()
				$("#mcq6Show").hide();
			break;
			case "mcq6":
				$("#mcq1Show").show()
				$("#mcq2Show").show()
				$("#mcq3Show").show()
				$("#mcq4Show").show()
				$("#mcq5Show").show()
				$("#mcq6Show").show();
			break;
			default:
				$("#mcq1Show").hide()
				$("#mcq2Show").hide()
				$("#mcq3Show").hide()
				$("#mcq4Show").hide()
				$("#mcq5Show").hide()
				$("#mcq6Show").hide();

			}
		});
	});
	</script>
	
		<script type="text/javascript">
		$('#level_id').on('change',function(){
			$value=$(this).val();
			$.ajax({
				type : 'get',
				url  : '{{URL:: to('/question_views/create')}}',
				data : {'level_id':$value},
				//dataType : 'text',
				success: function(data){
					//alert(data);
					$('#unit_id').html(data);
					//console.log($(this).data);
					}
				});
			})
	</script>
	
	@yield('scripts')

</body>
</html>
