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
                        <li><a href="{{ url('/home') }}">Home</a></li>
                        <li><a href="{{ Route('question_views.create') }}">Create Question</a></li>
                        <li><a href="{{ url('/question_views') }}">Manage Questions</a></li>
                        <li><a href="{{ url('/level_views') }}">Manage Levels</a></li>
                        <li><a href="{{ url('/unit_views') }}">Manage Units</a></li>
                        <li><a href="{{ url('/topic_views') }}">Manage Topics</a></li>
                        <li><a href="{{ url('/lesson_views') }}">Manage Lessons</a></li>
                        @if(auth()->user()->is_admin)
                            <li><a href="{{ route('users.index') }}">Manage Users</a></li>
                        @endif
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
		
		var questionType = $("#type option:selected").val();
		var repMode = $("#reply_mode option:selected").val();
		var answerSize = $("#answer_size option:selected").val();
		
		switch(questionType){
			case "text":
			$("#imageShow").hide();
			$("#shapeShow").hide();
			break;
			case "image":
			$("#imageShow").show();
			$("#shapeShow").hide();
			break;
			case "draw":
			$("#imageShow").hide();
			$("#shapeShow").show();
			break;
			default:
			
			}
		
		if(repMode == "FB"){
			$("#questionShow").hide();
			switch(answerSize){
				case "1":
				//Question parts show and hide
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").hide();
				$("#fillqp4Show").hide();
				$("#fillqp5Show").hide();
				$("#fillqp6Show").hide();
				$("#fillqp7Show").hide();
				
				//Answers show and hide
				
				$("#answerShow").show();
				$("#answer2Show").hide();
				$("#answer3Show").hide();
				$("#answer4Show").hide();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
			break;
			case "2":
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").show();
				$("#fillqp4Show").hide();
				$("#fillqp5Show").hide();
				$("#fillqp6Show").hide();
				$("#fillqp7Show").hide();
				//Answers show and hide
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").hide();
				$("#answer4Show").hide();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
			break;
			case "3":
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").show();
				$("#fillqp4Show").show();
				$("#fillqp5Show").hide();
				$("#fillqp6Show").hide();
				$("#fillqp7Show").hide();
				//Answers show and hide
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").hide();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
			break;
			case "4":
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").show();
				$("#fillqp4Show").show();
				$("#fillqp5Show").show();
				$("#fillqp6Show").hide();
				$("#fillqp7Show").hide();
				//Answers show and hide
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").show();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
			break;
			case "5":
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").show();
				$("#fillqp4Show").show();
				$("#fillqp5Show").show();
				$("#fillqp6Show").show();
				$("#fillqp7Show").hide();
				//Answers show and hide
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").show();
				$("#answer5Show").show();
				$("#answer6Show").hide();
			break;
			case "6":
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").show();
				$("#fillqp4Show").show();
				$("#fillqp5Show").show();
				$("#fillqp6Show").show();
				$("#fillqp7Show").show();
				//Answers show and hide
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").show();
				$("#answer5Show").show();
				$("#answer6Show").show();
			break;
			default:
				$("#fillqp1Show").hide();
				$("#fillqp2Show").hide();
				$("#fillqp3Show").hide();
				$("#fillqp4Show").hide();
				$("#fillqp5Show").hide();
				$("#fillqp6Show").hide();
				$("#fillqp7Show").hide();
				//Answers show and hide
				$("#mcq1Show").hide()
				$("#mcq2Show").hide()
				$("#mcq3Show").hide()
				$("#mcq4Show").hide()
				$("#mcq5Show").hide()
				$("#mcq6Show").hide();
			}

			
		}else{
				$("#questionShow").show();
				$("#fillShow").hide();
				switch(answerSize){
				case "1":
				$("#answerShow").show();
				$("#answer2Show").hide();
				$("#answer3Show").hide();
				$("#answer4Show").hide();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
			break;
			case "2":
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").hide();
				$("#answer4Show").hide();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
				
			break;
			case "3":
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").hide();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
			break;
			case "4":
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").show();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
			break;
			case "5":
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").show();
				$("#answer5Show").show();
				$("#answer6Show").hide();
			break;
			case "6":
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").show();
				$("#answer5Show").show();
				$("#answer6Show").show();
			break;
			default:
				$("#answerShow").show();
				$("#answer2Show").hide();
				$("#answer3Show").hide();
				$("#answer4Show").hide();
				$("#answer5Show").hide();
				$("#answer6Show").hide();

			}

		}
		
		switch(repMode){
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
			var answerSize = $("#answer_size option:selected").val();
			var selection = $(this).val();
			if(selection == "FB"){
				$("#questionShow").hide();
				$("#fillShow").show();
				
				switch(answerSize){
				case "1":
				//Question parts show and hide
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").hide();
				$("#fillqp4Show").hide();
				$("#fillqp5Show").hide();
				$("#fillqp6Show").hide();
				$("#fillqp7Show").hide();
			break;
			case "2":
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").show();
				$("#fillqp4Show").hide();
				$("#fillqp5Show").hide();
				$("#fillqp6Show").hide();
				$("#fillqp7Show").hide();
			break;
			case "3":
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").show();
				$("#fillqp4Show").show();
				$("#fillqp5Show").hide();
				$("#fillqp6Show").hide();
				$("#fillqp7Show").hide();
			break;
			case "4":
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").show();
				$("#fillqp4Show").show();
				$("#fillqp5Show").show();
				$("#fillqp6Show").hide();
				$("#fillqp7Show").hide();
			break;
			case "5":
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").show();
				$("#fillqp4Show").show();
				$("#fillqp5Show").show();
				$("#fillqp6Show").show();
				$("#fillqp7Show").hide();
			break;
			case "6":
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").show();
				$("#fillqp4Show").show();
				$("#fillqp5Show").show();
				$("#fillqp6Show").show();
				$("#fillqp7Show").show();
			break;
			default:
				$("#fillqp1Show").hide();
				$("#fillqp2Show").hide();
				$("#fillqp3Show").hide();
				$("#fillqp4Show").hide();
				$("#fillqp5Show").hide();
				$("#fillqp6Show").hide();
				$("#fillqp7Show").hide();
			}
				} else{
					$("#questionShow").show();
					$("#fillShow").hide();
					}
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
		
		$("#answer_size").on('change',function(){
			var repMode = $("#reply_mode option:selected").val();
		    var answerSize = $(this).val();
			
			if(repMode == "FB"){
			$("#questionShow").hide();
			$("#fillShow").show();
			switch(answerSize){
				case "1":
				//Question parts show and hide
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").hide();
				$("#fillqp4Show").hide();
				$("#fillqp5Show").hide();
				$("#fillqp6Show").hide();
				$("#fillqp7Show").hide();
				
				//Answers show and hide
				
				$("#answerShow").show();
				$("#answer2Show").hide();
				$("#answer3Show").hide();
				$("#answer4Show").hide();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
			break;
			case "2":
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").show();
				$("#fillqp4Show").hide();
				$("#fillqp5Show").hide();
				$("#fillqp6Show").hide();
				$("#fillqp7Show").hide();
				//Answers show and hide
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").hide();
				$("#answer4Show").hide();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
			break;
			case "3":
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").show();
				$("#fillqp4Show").show();
				$("#fillqp5Show").hide();
				$("#fillqp6Show").hide();
				$("#fillqp7Show").hide();
				//Answers show and hide
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").hide();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
			break;
			case "4":
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").show();
				$("#fillqp4Show").show();
				$("#fillqp5Show").show();
				$("#fillqp6Show").hide();
				$("#fillqp7Show").hide();
				//Answers show and hide
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").show();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
			break;
			case "5":
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").show();
				$("#fillqp4Show").show();
				$("#fillqp5Show").show();
				$("#fillqp6Show").show();
				$("#fillqp7Show").hide();
				//Answers show and hide
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").show();
				$("#answer5Show").show();
				$("#answer6Show").hide();
			break;
			case "6":
				$("#fillqp1Show").show();
				$("#fillqp2Show").show();
				$("#fillqp3Show").show();
				$("#fillqp4Show").show();
				$("#fillqp5Show").show();
				$("#fillqp6Show").show();
				$("#fillqp7Show").show();
				//Answers show and hide
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").show();
				$("#answer5Show").show();
				$("#answer6Show").show();
			break;
			default:
				$("#fillqp1Show").hide();
				$("#fillqp2Show").hide();
				$("#fillqp3Show").hide();
				$("#fillqp4Show").hide();
				$("#fillqp5Show").hide();
				$("#fillqp6Show").hide();
				$("#fillqp7Show").hide();
				//Answers show and hide
				$("#mcq1Show").hide();
				$("#mcq2Show").hide();
				$("#mcq3Show").hide();
				$("#mcq4Show").hide();
				$("#mcq5Show").hide();
				$("#mcq6Show").hide();
			}
			
		}else{
				$("#questionShow").show();
				$("#fillShow").hide();
				switch(answerSize){
				case "1":
				$("#answerShow").show();
				$("#answer2Show").hide();
				$("#answer3Show").hide();
				$("#answer4Show").hide();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
			break;
			case "2":
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").hide();
				$("#answer4Show").hide();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
				
			break;
			case "3":
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").hide();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
			break;
			case "4":
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").show();
				$("#answer5Show").hide();
				$("#answer6Show").hide();
			break;
			case "5":
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").show();
				$("#answer5Show").show();
				$("#answer6Show").hide();
			break;
			case "6":
				$("#answerShow").show();
				$("#answer2Show").show();
				$("#answer3Show").show();
				$("#answer4Show").show();
				$("#answer5Show").show();
				$("#answer6Show").show();
			break;
			default:
				$("#answerShow").show();
				$("#answer2Show").hide();
				$("#answer3Show").hide();
				$("#answer4Show").hide();
				$("#answer5Show").hide();
				$("#answer6Show").hide();

			}

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
				success: function(data){
					$('#unit_id').html(data);
					}
				});
			});
		$('#unit_id').on('change',function(){
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
			
	$('#topic_id').on('change',function(){
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
