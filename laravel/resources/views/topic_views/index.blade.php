@extends('layouts.app')

@section('content')

<div class="container">
	<div class="panel panel-default">
		<div class="panel-heading clearfix"> 
			<div class="panel-title pull-left">
             Search and Create!
			</div>
			<div class="pull-right">
				<a class="btn btn-primary" href="{{ route('topic_views.create') }}">Create</a>

			</div>
	</div>

		<div class="panel-body">
		
			<div class="row">
				<div class="col-md-12">
					<form class="form-horizontal" role="form" action="{{ route('topic_views.index') }}" method="GET">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

				        <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                            <label for="level_id" class="col-md-4 control-label">Level</label>

                            <div class="col-md-6">
							      <select class="form-control" name="level_id" id="level_id">
								  
								  @if (count($levels) > 0)
									  <option value="">Select From ...</option>
										@foreach($levels as $level)
											<option value="{{$level->id}}" 
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
											<option value="{{$unit->id}}" 
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
						
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button class="btn btn-primary" type="submit" >Search</button>
							</div>
						</div>
						</form>
						
						<div class="row">
							<div class="col.md.12">
								<div class="table-responsive">
									<table class="table table-striped">
										<thead>
											<tr>
												<th class="col-md">Topic ID</th>
												<th class="col-md">Topic Title</th>
												<th class="col-md">Dependency</th>
												<th class="col-md-3">OPTIONS</th>
											</tr>
										</thead>


						<tbody>
							@foreach($topics as $topic)
								<tr>
									<td>{{$topic->id}}</td>
									<td>{{$topic->title}}</td>
									<td>{{$topic->dependency}}</td>
									<td class="text-right">

											<a class="btn btn-primary" href="{{ route('topic_views.show', $topic->id) }}">View</a>
											<a class="btn btn-warning" href="{{ route('topic_views.edit', $topic->id) }}">Edit</a>
											<form action="{{ route('topic_views.destroy', $topic->id) }}" 
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
			</div>
		</div>
	</div>
</div>
</div>


@endsection