@extends('layouts.app')

@section('content')
 
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">CalendarEvents / Edit</div>

                <div class="panel-body">

    <div class="row">
        <div class="col-md-12">

            <form class="form-horizontal" role="form" action="{{ route('question_views.update', $question->id) }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

				        <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                            <label for="level_id" class="col-md-4 control-label">Level</label>

                            <div class="col-md-6">
							      <select class="form-control" name="level_id" id="level_id">
								  
								  @if (count($levels) > 0)
									  <option value="">Select From ...</option>
										@foreach($levels as $level)
											<option value="{{$level->id}}" @if (old("level_id") == $level->id) selected="selected" @endif  @if ( $level->id == $question->lid) selected="selected" 
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
											<option value="{{$unit->id}}" @if ( $unit->id == $question->uid) selected="selected" 
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
											<option value="{{$topic->id}}" @if ( $topic->id == $question->tid) selected="selected" 
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
											<option value="{{$lesson->id}}" @if ( $lesson->id == $question->lesson_id) selected="selected" 
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
										@if (old('type') == $qtype->code || $question->type == $qtype->code)
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
						<div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
                            <label for="question" class="col-md-4 control-label">Question</label>

                            <div class="col-md-6">
                                <textarea id="question" class="form-control"  name="question"> {{$question->question}}</textarea>

                                @if ($errors->has('question'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('question') }}</strong>
                                    </span>
                                @endif
                            </div>
				      </div>
					  
						<div class="form-group{{ $errors->has('reply_mode') ? ' has-error' : '' }}">
                            <label for="reply_mode" class="col-md-4 control-label">REPLY MODE</label>

                            <div class="col-md-6">
							      <select class="form-control" name="reply_mode"id="reply_mode">
										 @foreach($qrmodes as $qrmode)
												@if (old('reply_mode') == $qrmode->code || $question->reply_mode == $qrmode->code)
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
				
				<div class="form-group{{ $errors->has('mandatoriness') ? ' has-error' : '' }}">
						<label for="type" class="col-md-4 control-label">Mandatoriness</label>

                            <div class="col-md-6 radio"> 
								@if ( "Yes" == $question->mandatoriness)
								<label for="type" class="col-md-3"> <input type="radio" name="mandatoriness" checked="checked" value="Yes">Mandatory</label>
								<label for="type" class="col-md-3"> <input type="radio" name="mandatoriness" value="No"> Optional</label>
								@endif
								@if ( "No" == $question->mandatoriness)
								<label for="type" class="col-md-3"> <input type="radio" name="mandatoriness"  value="Yes">Mandatory</label>
								<label for="type" class="col-md-3"> <input type="radio" name="mandatoriness" checked="checked" value="No"> Optional</label>
								@endif

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
                                <input id="min_value" type="text" class="form-control"  name="min_value" value="{{$question->min_value}}">

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
                                <input id="ini_position" type="text" class="form-control"  name="ini_position"  value="{{$question->initial_position}}">

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
                                <input id="step_value" type="text" class="form-control"  name="step_value" value="{{$question->step_value}}">

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
                                <input id="max_value" type="text" class="form-control"  name="max_value" value="{{$question->max_value}}">

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
                                <input id="mcq1" type="text" class="form-control"  name="mcq1" value="{{$question->mcq1}}">

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
                                <input id="mcq2" type="text" class="form-control"  name="mcq2" value="{{$question->mcq2}}">

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
                                <input id="mcq3" type="text" class="form-control"  name="mcq3" value="{{$question->mcq3}}">

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
                                <input id="mcq4" type="text" class="form-control"  name="mcq4" value="{{$question->mcq4}}">

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
                                <input id="mcq5" type="text" class="form-control"  name="mcq5" value="{{$question->mcq5}}">

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
                                <input id="mcq6" type="text" class="form-control"  name="mcq6" value="{{$question->mcq6}}">

                                @if ($errors->has('mcq6'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mcq6') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				
				<div class="form-group{{ $errors->has('answer') ? ' has-error' : '' }}">
                            <label for="answer" class="col-md-4 control-label">Answer</label>

                            <div class="col-md-6">
                                <input id="answer" type="text" class="form-control"  name="answer" value="{{$question->answer}}">

                                @if ($errors->has('answer'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('answer') }}</strong>
                                    </span>
                                @endif
                            </div>
				</div>
				
									  
					  <div class="form-group{{ $errors->has('feedback') ? ' has-error' : '' }}">
                            <label for="feedback" class="col-md-4 control-label">Feedback</label>

                            <div class="col-md-6">
                                <textarea id="feedback" class="form-control"  name="feedback"> {{$question->feedback}}</textarea>

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
                                <textarea id="explanation" class="form-control"  name="explanation">{{$question->explanation}}</textarea>

                                @if ($errors->has('explanation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('explanation') }}</strong>
                                    </span>
                                @endif
                            </div>
				      </div> 



            <a class="btn btn-default" href="{{ route('question_views.index') }}">Back</a>
            <button class="btn btn-primary" type="submit" >Save</button>
            </form>
        </div>
    </div>
	
	          </div>
            </div>
        </div>
    </div>
</div>


@endsection