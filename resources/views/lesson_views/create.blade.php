@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Lesson / Create </div>
				 
				 <div class="panel-body">
				 @if(Session::has('flash_message'))
					<div id="successMessage" class="alert alert-success">
						<span class="glyphicon glyphicon-ok"></span>
							<em> {!! session('flash_message') !!}</em>
					</div>
				@endif
					<form class="form-horizontal" role="form" action="{{ route('lesson_views.store') }}" method="POST">
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
											<option value="{{$unit->id}}" @if ( $unit->id == $uid) selected="selected" 
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
											<option value="{{$topic->id}}" @if ( $topic->id == $tid) selected="selected" 
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
                            <label for="lesson_title" class="col-md-4 control-label">Lesson Title</label>

                            <div class="col-md-6">
                                <textarea id="lesson_title" class="form-control"  name="lesson_title" placeholder="Enter question text.."> {{ old('lesson_title') }}</textarea>

                                @if ($errors->has('lesson_title'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('lesson_title') }}</strong>
                                    </span>
                                @endif
                            </div>
				      </div>
			
				<div class="form-group{{ $errors->has('dependency') ? ' has-error' : '' }}">
                            <label for="type" class="col-md-4 control-label">Dependency</label>

                            <div class="col-md-6 radio"> 
								<label for="type" class="col-md-3"> <input checked="checked" type="radio" name="dependency" value="Yes">Yes</label>
							    <label for="type" class="col-md-3"> <input type="radio" name="dependency" value="No"> No</label>

                                @if ($errors->has('dependency'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('dependency') }}</strong>
                                    </span>
                                @endif
                            </div>
					</div>



		<div class="form-group">
			<div class="col-md-6 col-md-offset-4">
               <a class="btn btn-default" href="{{ route('lesson_views.index') }}">Back</a>
                  <button class="btn btn-primary" type="submit" >Submit</button>
		     </div>
		</div>
            </form>
			@if (count($lessons) > 0)
			<div class="row">
							<div class="col.md.12">
								<div class="table-responsive">
									<table class="table table-striped">
										<thead>
											<tr>
												<th class="col-md">Lesson ID</th>
												<th class="col-md">Lesson Title</th>
												<th class="col-md">Dependency</th>
												<th class="col-md-3">OPTIONS</th>
											</tr>
										</thead>


						<tbody>
							@foreach($lessons as $lesson)
								<tr>
									<td>{{$lesson->id}}</td>
									<td>{{$lesson->title}}</td>
									<td>{{$lesson->dependency}}</td>
									<td class="text-right">

											<a class="btn btn-primary disabled" href="{{ route('lesson_views.show', $lesson->id) }}">View</a>
											<a class="btn btn-warning" href="{{ route('lesson_views.edit', $lesson->id) }}">Edit</a>
											<form action="{{ route('lesson_views.destroy', $lesson->id) }}" 
												method="POST" style="display: inline;" 
												onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
												<input type="hidden" name="_method" value="DELETE">
												<input type="hidden" name="_token" value="{{ csrf_token() }}"> 
												<button class="btn btn-danger" type="submit">Delete</button>
												</form>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
							
				</div>
						
			</div>
		</div>
		@endif

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
