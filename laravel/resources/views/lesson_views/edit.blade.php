@extends('layouts.app')

@section('content')
 
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><div class="panel-title">
             Edit Lesson
			</div></div>

                <div class="panel-body">

    <div class="row">
        <div class="col-md-12">

            <form class="form-horizontal" role="form" action="{{ route('lesson_views.update', $lesson->id) }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

				        <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                            <label for="level_id" class="col-md-4 control-label">Level</label>

                            <div class="col-md-6">
							      <select class="form-control" name="level_id" id="level_id">
								  
								  @if (count($levels) > 0)
									  <option value="">Select From ...</option>
										@foreach($levels as $level)
											<option value="{{$level->id}}" @if (old("level_id") == $level->id) selected="selected" @endif  @if ( $level->id == $lesson->lid) selected="selected" 
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
											<option value="{{$unit->id}}" @if ( $unit->id == $lesson->uid) selected="selected" 
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
											<option value="{{$topic->id}}" @if ( $topic->id == $lesson->tid) selected="selected" 
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
						
						<div class="form-group{{ $errors->has('lesson_title') ? ' has-error' : '' }}">
                            <label for="lesson_title" class="col-md-4 control-label">Title</label>

                            <div class="col-md-6">
                                <textarea id="lesson_title" class="form-control"  name="lesson_title"> {{$lesson->title}}</textarea>

                                @if ($errors->has('lesson_title'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('lesson_title') }}</strong>
                                    </span>
                                @endif
                            </div>
				      </div>
				      	<div class="form-group{{ $errors->has('randomisation') ? ' has-error' : '' }}">
							<label for="type" class="col-md-4 control-label">Turn on randomisation</label>

							<div class="col-md-6 radio">
								<label for="type" class="col-md-3"> <input {{ ($lesson->randomisation == true) ? 'checked="checked"' : ''}} type="checkbox" name="randomisation" value="1"></label>
								@if ($errors->has('randomisation'))
									<span class="help-block">
										<strong>{{ $errors->first('randomisation') }}</strong>
										</span>
								@endif
							</div>
						</div>
						<div class="form-group{{ $errors->has('dependency') ? ' has-error' : '' }}">
							<label for="type" class="col-md-4 control-label">This should be finished to continue</label>

							<div class="col-md-6 radio">
								<label for="type" class="col-md-3"> <input {{ ($lesson->dependency == true) ? 'checked="checked"' : ''}} type="checkbox" name="dependency" value="1"></label>
								@if ($errors->has('dependency'))
									<span class="help-block">
												<strong>{{ $errors->first('dependency') }}</strong>
											</span>
								@endif
							</div>
						</div>
						<div class="form-group{{ $errors->has('dev_mode') ? ' has-error' : '' }}">
							<label for="type" class="col-md-4 control-label">Lesson in development</label>
							<div class="col-md-6 radio">
								<label for="type" class="col-md-3"> <input {{ ($lesson->dev_mode == true) ? 'checked="checked"' : ''}} type="checkbox" name="dev_mode" value="1"></label>
								@if ($errors->has('dev_mode'))
									<span class="help-block">
												<strong>{{ $errors->first('dev_mode') }}</strong>
											</span>
								@endif
							</div>
						</div>

						<div class="form-group{{ $errors->has('order_no') ? ' has-error' : '' }}">
	                        <label for="order_no" class="col-md-4 control-label">Order No</label>

	                        <div class="col-md-6">
							      <select class="form-control" name="order_no" id="order_no">
									<option value="1">1</option>
								    @if ($total_lesson > 0)
									  	@for($count = 2; $count <= $total_lesson + 1; $count++)
											<option <?php echo ($count == $lesson->order_no) ? 'selected="selected"' : ''; ?> value="{{$count}}">{{$count}}</option>
										@endfor
								  	@endif
									</select>
	                            
	                            @if ($errors->has('order_no'))
	                                <span class="help-block">
	                                    <strong>{{ $errors->first('order_no') }}</strong>
	                                </span>
	                            @endif
	                        </div>
	                    </div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
				            <a class="btn btn-default" href="{{ route('lesson_views.create') }}">Back</a>
				            <button class="btn btn-primary" type="submit" >Update</button>
							
							</div>
						</div>
		            </form>
		        </div>
		    </div>
	
	          </div>
            </div>
        </div>
    </div>
</div>


@endsection
