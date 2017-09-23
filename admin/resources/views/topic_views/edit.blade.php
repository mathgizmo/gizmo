@extends('layouts.app')

@section('content')
 
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><div class="panel-title">
             Edit Topic
			</div></div>

                <div class="panel-body">

    <div class="row">
        <div class="col-md-12">

            <form class="form-horizontal" role="form" action="{{ route('topic_views.update', $topic->id) }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

				        <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                            <label for="level_id" class="col-md-4 control-label">Level</label>

                            <div class="col-md-6">
							      <select class="form-control" name="level_id" id="level_id">
								  
								  @if (count($levels) > 0)
									  <option value="">Select From ...</option>
										@foreach($levels as $level)
											<option value="{{$level->id}}" @if (old("level_id") == $level->id) selected="selected" @endif  @if ( $level->id == $topic->lid) selected="selected" 
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
											<option value="{{$unit->id}}" @if ( $unit->id == $topic->uid) selected="selected" 
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
						
						<div class="form-group{{ $errors->has('topic_title') ? ' has-error' : '' }}">
                            <label for="topic_title" class="col-md-4 control-label">Title</label>

                            <div class="col-md-6">
                                <textarea id="topic_title" class="form-control"  name="topic_title"> {{$topic->title}}</textarea>

                                @if ($errors->has('topic_title'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('topic_title') }}</strong>
                                    </span>
                                @endif
                            </div>
				      </div>
					  
				<div class="form-group{{ $errors->has('dependency') ? ' has-error' : '' }}">
						<label for="type" class="col-md-4 control-label">Dependency</label>

                            <div class="col-md-6 radio"> 
								@if ( "Yes" == $topic->dependency)
								<label for="type" class="col-md-3"> <input type="radio" name="dependency" checked="checked" value="Yes">Yes</label>
								<label for="type" class="col-md-3"> <input type="radio" name="dependency" value="No"> No</label>
								@endif
								@if ( "No" == $topic->dependency)
								<label for="type" class="col-md-3"> <input type="radio" name="dependency"  value="Yes">Yes</label>
								<label for="type" class="col-md-3"> <input type="radio" name="dependency" checked="checked" value="No"> No</label>
								@endif

                                @if ($errors->has('dependency'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('dependency') }}</strong>
                                    </span>
                                @endif
                            </div>
					</div>
<div class="form-group">
			<div class="col-md-6 col-md-offset-4">
            <a class="btn btn-default" href="{{ route('topic_views.create') }}">Back</a>
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
