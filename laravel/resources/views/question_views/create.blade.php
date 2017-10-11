@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Question / Create </div>
				 
				 <div class="panel-body">
					@if(Session::has('flash_message'))
						<div id="successMessage" class="alert alert-success">
							<span class="glyphicon glyphicon-ok"></span>
								<em> {!! session('flash_message') !!}</em>
						</div>
					@endif
					
					<form class="form-horizontal" role="form" action="{{ route('question_views.store') }}" method="POST">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

				        <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                            <label for="level_id" class="col-md-4 control-label">Level</label>

                            <div class="col-md-6">
							      <select class="form-control" name="level_id" id="level_id">
								  
								  @if (count($levels) > 0)
									  <option value="">Select From ...</option>
										@foreach($levels as $level)
											<option value="{{$level->id}}" @if (old("level_id") == $level->id) selected="selected" @endif  @if ( $level->id == $lid) selected="selected"
											@endif 
											>{{$level->title}}</option>
										@endforeach
								 @endif
									</select>
                                
                                @if ($errors->has('level_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('level_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div id="unit_options"> </div>
						<div class="form-group{{ $errors->has('unit_id') ? ' has-error' : '' }}">
                            <label for="unit_id" class="col-md-4 control-label">Unit</label>

                            <div class="col-md-6">
							      <select class="form-control" name="unit_id" id="unit_id">
								   @if (count($units) > 0)
								  @foreach($units as $unit)
											<option value="{{$unit->id}}" @if (old("unit_id") == $unit->id) selected="selected" @endif  @if ( $unit->id == $uid) selected="selected"
											@endif 
											>{{$unit->title}}</option>
										@endforeach
								  @endif
									</select>
                                
                                @if ($errors->has('unit_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('unit_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div class="form-group{{ $errors->has('topic_id') ? ' has-error' : '' }}">
                            <label for="topic_id" class="col-md-4 control-label">Topic</label>

                            <div class="col-md-6">
							      <select class="form-control" name="topic_id" id="topic_id">
								  
								  @if (count($topics) > 0)
								  @foreach($topics as $topic)
											<option value="{{$topic->id}}" @if (old("topic_id") == $topic->id) selected="selected" @endif  @if ( $topic->id == $tid) selected="selected"
											@endif 
											>{{$topic->title}}</option>
										@endforeach
								  @endif

									</select>
                                
                                @if ($errors->has('topic_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('topic_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div class="form-group{{ $errors->has('lesson_id') ? ' has-error' : '' }}">
                            <label for="lesson_id" class="col-md-4 control-label">Lesson</label>

                            <div class="col-md-6">
							      <select class="form-control" name="lesson_id" id="lesson_id">
									
									@if (count($lessons) > 0)
								  @foreach($lessons as $lesson)
											<option value="{{$lesson->id}}" @if (old("lesson_id") == $lesson->id) selected="selected" @endif  @if ( $lesson->id == $lsnid) selected="selected"
											@endif 
											>{{$lesson->title}}</option>
										@endforeach
								  @endif
									</select>
                                
                                @if ($errors->has('lesson_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lesson_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						 <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                            <label for="type" class="col-md-4 control-label">Type</label>

                            <div class="col-md-6">
								<select class="form-control" name="type" id="type">
								  @foreach($qtypes as $qtype)
										@if (old('type') == $qtype->code)
											<option value="{{$qtype->code}}" selected>{{$qtype->type}}</option>
										@else
											<option value="{{$qtype->code}}" >{{$qtype->type}}</option>
										@endif
										@endforeach
									</select>
                                
                                @if ($errors->has('type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
 
						<div class="form-group{{ $errors->has('reply_mode') ? ' has-error' : '' }}">
                            <label for="reply_mode" class="col-md-4 control-label">REPLY MODE</label>

                            <div class="col-md-6">
							      <select class="form-control" name="reply_mode" id="reply_mode">
										 @foreach($qrmodes as $qrmode)
												@if (old('reply_mode') == $qrmode->code)
											        <option value="{{$qrmode->code}}" selected>{{$qrmode->mode}}</option>
												@else 
													<option value="{{$qrmode->code}}">{{$qrmode->mode}}</option>
												@endif

										@endforeach
									</select>
                                
                                @if ($errors->has('reply_mode'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('reply_mode') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div class="form-group{{ $errors->has('answer_size') ? ' has-error' : '' }}">
                            <label for="answer_size" class="col-md-4 control-label">Answer Size</label>

                            <div class="col-md-6">
							      <select class="form-control" name="answer_size" id="answer_size">
									   <option value="1" 
											@if ( old('answer_size')  == "1") selected="selected" @endif >1</option>
										<option value="2" 
											@if ( old('answer_size')  == "2") selected="selected" @endif>2</option>
										<option value="3" 
											@if ( old('answer_size')  == "3") selected="selected" @endif>3</option>
										<option value="4" 
											@if ( old('answer_size')  == "4") selected="selected" @endif>4</option>
										<option value="5" 
											@if ( old('answer_size')  == "5") selected="selected" @endif>5</option>
										<option value="6" 
											@if ( old('answer_size')  == "6") selected="selected" @endif>6</option>
									</select>
                                
                                @if ($errors->has('answer_size'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('answer_size') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						
						<div id="questionShow" class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
                            <label for="question" class="col-md-4 control-label">Question</label>

                            <div class="col-md-6">
                                <textarea id="question" class="form-control"  name="question" placeholder="Enter question text.."> {{ old('question') }}</textarea>

                                @if ($errors->has('question'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('question') }}</strong>
                                    </span>
                                @endif
                            </div>
						</div>
						
				<div id="fillShow">
						<div id="fillqp1Show" class="form-group{{ $errors->has('question_fp1') ? ' has-error' : '' }}">
                            <label for="question_fp1" class="col-md-4 control-label"> Question Part1</label>

                            <div class="col-md-6">
                                <textarea id="question_fp1" class="form-control"  name="question_fp1" placeholder="Enter part 1 text.."> {{ old('question_fp1') }}</textarea>

                                @if ($errors->has('question_fp1'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('question_fp1') }}</strong>
                                    </span>
                                @endif
                            </div>
						</div>
						
						<div id="fillqp2Show" class="form-group{{ $errors->has('question_fp2') ? ' has-error' : '' }}">
                            <label for="question_fp1" class="col-md-4 control-label">Question Part2</label>

                            <div class="col-md-6">
                                <textarea id="question_fp1" class="form-control"  name="question_fp2" placeholder="Enter part 2 text.."> {{ old('question_fp2') }}</textarea>

                                @if ($errors->has('question_fp2'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('question_fp2') }}</strong>
                                    </span>
                                @endif
                            </div>
						</div>
						
						<div id="fillqp3Show" class="form-group{{ $errors->has('question_fp3') ? ' has-error' : '' }}">
                            <label for="question_fp3" class="col-md-4 control-label">Question Part3</label>

                            <div class="col-md-6">
                                <textarea id="question_fp3" class="form-control"  name="question_fp3" placeholder="Enter part 3 text.."> {{ old('question_fp3') }}</textarea>

                                @if ($errors->has('question_fp3'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('question_fp3') }}</strong>
                                    </span>
                                @endif
                            </div>
						</div>
						
						<div id="fillqp4Show" class="form-group{{ $errors->has('question_fp4') ? ' has-error' : '' }}">
                            <label for="question_fp4" class="col-md-4 control-label">Question Part4</label>

                            <div class="col-md-6">
                                <textarea id="question_fp4" class="form-control"  name="question_fp4" placeholder="Enter part 4 text.."> {{ old('question_fp4') }}</textarea>

                                @if ($errors->has('question_fp4'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('question_fp4') }}</strong>
                                    </span>
                                @endif
                            </div>
						</div>
						
						<div id="fillqp5Show" class="form-group{{ $errors->has('question_fp5') ? ' has-error' : '' }}">
                            <label for="question_fp5" class="col-md-4 control-label">Question Part5</label>

                            <div class="col-md-6">
                                <textarea id="question_fp5" class="form-control"  name="question_fp5" placeholder="Enter part 5 text.."> {{ old('question_fp5') }}</textarea>

                                @if ($errors->has('question_fp5'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('question_fp5') }}</strong>
                                    </span>
                                @endif
                            </div>
						</div>
						
						<div id="fillqp6Show" class="form-group{{ $errors->has('question_fp4') ? ' has-error' : '' }}">
                            <label for="question_fp6" class="col-md-4 control-label">Question Part6</label>

                            <div class="col-md-6">
                                <textarea id="question_fp6" class="form-control"  name="question_fp6" placeholder="Enter part 6 text.."> {{ old('question_fp6') }}</textarea>

                                @if ($errors->has('question_fp6'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('question_fp6') }}</strong>
                                    </span>
                                @endif
                            </div>
						</div>
						
						<div id="fillqp7Show" class="form-group{{ $errors->has('question_fp7') ? ' has-error' : '' }}">
                            <label for="question_fp7" class="col-md-4 control-label">Question Part7</label>

                            <div class="col-md-6">
                                <textarea id="question_fp7" class="form-control"  name="question_fp7" placeholder="Enter part 7 text.."> {{ old('question_fp7') }}</textarea>

                                @if ($errors->has('question_fp7'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('question_fp7') }}</strong>
                                    </span>
                                @endif
                            </div>
						</div>
					</div>
						

				<div class="form-group{{ $errors->has('mandatoriness') ? ' has-error' : '' }}">
                            <label for="type" class="col-md-4 control-label">Mandatoriness</label>

                            <div class="col-md-6 radio"> 
								<label for="type" class="col-md-3"> <input checked="checked" type="radio" name="mandatoriness" value="Yes">Mandatory</label>
							    <label for="type" class="col-md-3"> <input type="radio" name="mandatoriness" value="No"> Optional</label>

                                @if ($errors->has('mandatoriness'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mandatoriness') }}</strong>
                                    </span>
                                @endif
                            </div>
					</div>
					
					
					
					<div id="imageShow" class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                            <label for="image" class="col-md-4 control-label">Question Image</label>

                            <div class="col-md-6">
                                <input id="image" type="file" class="form-control"  name="image" accept="image/*">

                                @if ($errors->has('image'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('image') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				<div id="shapeShow">
						<div class="form-group{{ $errors->has('shape') ? ' has-error' : '' }}">
                            <label for="shape" class="col-md-4 control-label">Shape</label>

                            <div class="col-md-6">
							      <select class="form-control" name="shape" id="shape">
									   <option value="" selected>Select one shape from below</option>
										<option value="rectangle">Rectangle</option>
										<option value="slider">Slider</option>
										<option value="circle">Circle</option>
										<option value="blob">Random Looking Blob</option>
									</select>
                                
                                @if ($errors->has('shape'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('shape') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
				<div class="form-group{{ $errors->has('min_value') ? ' has-error' : '' }}">
                            <label for="min_value" class="col-md-4 control-label">Min Value</label>

                            <div class="col-md-6">
                                <input id="min_value" type="text" class="form-control"  name="min_value" placeholder="Minimul Value">

                                @if ($errors->has('min_value'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('min_value') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				<div class="form-group{{ $errors->has('ini_position') ? ' has-error' : '' }}">
                            <label for="ini_position" class="col-md-4 control-label">Initial Position</label>

                            <div class="col-md-6">
                                <input id="ini_position" type="text" class="form-control"  name="ini_position"  placeholder="ini_position">

                                @if ($errors->has('ini_position'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ini_position') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				<div class="form-group{{ $errors->has('step_value') ? ' has-error' : '' }}">
                            <label for="step_value" class="col-md-4 control-label">Step Value</label>

                            <div class="col-md-6">
                                <input id="step_value" type="text" class="form-control"  name="step_value" placeholder="step_value">

                                @if ($errors->has('step_value'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('step_value') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				<div class="form-group{{ $errors->has('max_value') ? ' has-error' : '' }}">
                            <label for="max_value" class="col-md-4 control-label">Max Value</label>

                            <div class="col-md-6">
                                <input id="max_value" type="text" class="form-control"  name="max_value" placeholder="max_value">

                                @if ($errors->has('max_value'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('max_value') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
		</div>
				<div id="mcq1Show" class="form-group{{ $errors->has('mcq1') ? ' has-error' : '' }}">
                            <label for="mcq1" class="col-md-4 control-label">MCQ-1</label>

                            <div class="col-md-6">
                                <input id="mcq1" type="text" class="form-control"  name="mcq1" placeholder=" mcq1 ">

                                @if ($errors->has('mcq1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mcq1') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				<div id="mcq2Show" class="form-group{{ $errors->has('mcq2') ? ' has-error' : '' }}">
                            <label for="mcq2" class="col-md-4 control-label">MCQ-2</label>

                            <div class="col-md-6">
                                <input id="mcq2" type="text" class="form-control"  name="mcq2" placeholder="mcq2">

                                @if ($errors->has('mcq2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mcq2') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				<div id="mcq3Show" class="form-group{{ $errors->has('mcq3') ? ' has-error' : '' }}">
                            <label for="mcq3" class="col-md-4 control-label">MCQ-3</label>

                            <div class="col-md-6">
                                <input id="mcq3" type="text" class="form-control"  name="mcq3" placeholder="mcq3">

                                @if ($errors->has('mcq3'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mcq3') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				<div id="mcq4Show" class="form-group{{ $errors->has('mcq4') ? ' has-error' : '' }}">
                            <label for="mcq4" class="col-md-4 control-label">MCQ-4</label>

                            <div class="col-md-6">
                                <input id="mcq4" type="text" class="form-control"  name="mcq4" placeholder="mcq4">

                                @if ($errors->has('mcq4'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mcq4') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				<div id="mcq5Show" class="form-group{{ $errors->has('mcq5') ? ' has-error' : '' }}">
                            <label for="mcq5" class="col-md-4 control-label">MCQ-5</label>

                            <div class="col-md-6">
                                <input id="mcq5" type="text" class="form-control"  name="mcq5" placeholder="mcq5">

                                @if ($errors->has('mcq5'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mcq5') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				<div id="mcq6Show" class="form-group{{ $errors->has('mcq6') ? ' has-error' : '' }}">
                            <label for="mcq6" class="col-md-4 control-label">MCQ-6</label>

                            <div class="col-md-6">
                                <input id="mcq6" type="text" class="form-control"  name="mcq6" placeholder="mcq6">

                                @if ($errors->has('mcq6'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mcq6') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				
				<div  id="answerShow" class="form-group{{ $errors->has('answer') ? ' has-error' : '' }}">
                            <label for="answer" class="col-md-4 control-label">Answer</label>

                            <div class="col-md-6">
                                <input id="answer" type="text" class="form-control"  name="answer" value="{{ old('answer') }}" placeholder="single / 1st answer ">

                                @if ($errors->has('answer'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('answer') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				
				<div id="answer2Show" class="form-group{{ $errors->has('answer2') ? ' has-error' : '' }}">
                            <label for="answer2" class="col-md-4 control-label">2ND Answer</label>

                            <div class="col-md-6">
                                <input id="answer2" type="text" class="form-control"  name="answer2" value="{{ old('answer2') }}" placeholder="answer 2">

                                @if ($errors->has('answer2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('answer2') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				
				<div id="answer3Show" class="form-group{{ $errors->has('answer3') ? ' has-error' : '' }}">
                            <label for="answer3" class="col-md-4 control-label">3RD Answer</label>

                            <div class="col-md-6">
                                <input id="answer3" type="text" class="form-control"  name="answer3" value="{{ old('answer3') }}" placeholder="answer 3">

                                @if ($errors->has('answer3'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('answer3') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				
				<div id="answer4Show" class="form-group{{ $errors->has('answer4') ? ' has-error' : '' }}">
                            <label for="answer4" class="col-md-4 control-label">4TH Answer</label>

                            <div class="col-md-6">
                                <input id="answer4" type="text" class="form-control"  name="answer4" value="{{ old('answer4') }}" placeholder="answer 4">

                                @if ($errors->has('answer4'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('answer4') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				
				<div id="answer5Show" class="form-group{{ $errors->has('answer5') ? ' has-error' : '' }}">
                            <label for="answer5" class="col-md-4 control-label">5TH Answer</label>

                            <div class="col-md-6">
                                <input id="answer5" type="text" class="form-control"  name="answer5" value="{{ old('answer5') }}" placeholder="answer 5">

                                @if ($errors->has('answer5'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('answer5') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				
				<div id="answer6Show" class="form-group{{ $errors->has('answer6') ? ' has-error' : '' }}">
                            <label for="answer6" class="col-md-4 control-label">6TH Answer</label>

                            <div class="col-md-6">
                                <input id="answer6" type="text" class="form-control"  name="answer6" value="{{ old('answer6') }}" placeholder="answer 6">

                                @if ($errors->has('answer6'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('answer6') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				
									  
					  <div class="form-group{{ $errors->has('feedback') ? ' has-error' : '' }}">
                            <label for="feedback" class="col-md-4 control-label">Feedback</label>

                            <div class="col-md-6">
                                <textarea id="feedback" class="form-control"  name="feedback" placeholder="Enter Feedback text.."></textarea>

                                @if ($errors->has('feedback'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('feedback') }}</strong>
                                    </span>
                                @endif
                            </div>
				      </div>
					  
					  <div class="form-group{{ $errors->has('explanation') ? ' has-error' : '' }}">
                            <label for="explanation" class="col-md-4 control-label">Explanation</label>

                            <div class="col-md-6">
                                <textarea id="explanation" class="form-control"  name="explanation" placeholder="Enter explanation text.."></textarea>

                                @if ($errors->has('explanation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('explanation') }}</strong>
                                    </span>
                                @endif
                            </div>
				      </div> 

		<div class="form-group">
			<div class="col-md-6 col-md-offset-4">
               <a class="btn btn-default" href="{{ route('question_views.index') }}">Back</a>
                  <button class="btn btn-primary" type="submit" >Submit</button>
		     </div>
		</div>
            </form>

              </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
$(document).ready(function(){
        setTimeout(function() {
          $('#successMessage').fadeOut('fast');
        }, 4000); // <-- time in milliseconds
    });
</script>

@endsection
