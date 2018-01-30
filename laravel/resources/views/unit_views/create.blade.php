@extends('layouts.app')
@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Unit / Create </div>
				 <div class="panel-body">
				 @if(Session::has('flash_message'))
					<div id="successMessage" class="alert alert-success">
						<span class="glyphicon glyphicon-ok"></span>
							<em> {!! session('flash_message') !!}</em>
					</div>
				@endif
					<form class="form-horizontal" role="form" action="{{ route('unit_views.store') }}" method="POST">
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

						<div class="form-group{{ $errors->has('unit_title') ? ' has-error' : '' }}">
                            <label for="unit_title" class="col-md-4 control-label">Unit Title</label>

                            <div class="col-md-6">
                                <textarea id="unit_title" class="form-control"  name="unit_title" placeholder="Enter Unit text.."> {{ old('unit_title') }}</textarea>

                                @if ($errors->has('unit_title'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('unit_title') }}</strong>
                                    </span>
                                @endif
                            </div>
				      	</div>

						<div class="form-group{{ $errors->has('dependency') ? ' has-error' : '' }}">
							<label for="type" class="col-md-4 control-label">This should be finished to continue</label>

							<div class="col-md-6 radio">
								<label for="type" class="col-md-3"> <input checked="checked" type="checkbox" name="dependency" value="1"></label>
								@if ($errors->has('dependency'))
									<span class="help-block">
                                        <strong>{{ $errors->first('dependency') }}</strong>
                                    </span>
								@endif
							</div>
						</div>

						<div class="form-group{{ $errors->has('order_no') ? ' has-error' : '' }}">
	                        <label for="order_no" class="col-md-4 control-label">Order No</label>

	                        <div class="col-md-6">
							      <select class="form-control" name="order_no" id="order_no">
									<option value="1">1</option>
								    @if ($total_unit > 0)
									  	@for($count = 2; $count <= $total_unit + 1; $count++)
											<option <?php echo ($count > $total_unit) ? 'selected="selected"' : ''; ?> value="{{$count}}">{{$count}}</option>
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
				               <a class="btn btn-default" href="{{ route('unit_views.index') }}">Back</a>
				                  <button class="btn btn-primary" type="submit" >Submit</button>
						     </div>
						</div>
            </form>
			@if (count($units) > 0)
			<div class="row">
				<div class="col.md.12">
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th class="col-md">ID</th>
								<th class="col-md">Order No</th>
								<th class="col-md">Title</th>
								<th class="col-md">Dependency</th>
								<th class="col-md-3">OPTIONS</th>
							</tr>
						</thead>


						<tbody>
							@foreach($units as $unit)
								<tr>
									<td>{{$unit->id}}</td>
									<td>{{$unit->order_no}}</td>
									<td>{{$unit->title}}</td>
									<td>{{($unit->dependency == true) ? 'Yes' : 'No'}}</td>
									<td class="text-right">

										<!-- <a class="btn btn-primary" href="{{ route('unit_views.show', $unit->id) }}">View</a> -->
										<a class="btn btn-warning" href="{{ route('unit_views.edit', $unit->id) }}">Edit</a>
										<form action="{{ route('unit_views.destroy', $unit->id) }}" 
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
