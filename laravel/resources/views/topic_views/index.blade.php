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
											<option <?php echo ($level_id == $level->id) ? 'selected="selected"' : ''; ?> value="{{$level->id}}"
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
											<option <?php echo ($unit_id == $unit->id) ? 'selected="selected"' : ''; ?> value="{{$unit->id}}"
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
							@if(Session::has('message'))
							<div id="successMessage" class="alert alert-success">
								<span class="glyphicon glyphicon-ok"></span>
								<em> {!! session('message') !!}</em>
							</div>
							@endif
							<div class="col-md-12">
								<div class="table-responsive">
									<table class="table table-striped">
										<thead>
											<tr>
												<th class="col-md">Image</th>
												<th class="col-md">Order No</th>
												<th class="col-md">ID</th>
												<th class="col-md">Title</th>
												<th class="col-md">Short Name</th>
												<th class="col-md-3">OPTIONS</th>
											</tr>
										</thead>


						<tbody>
							@foreach($topics as $topic)
								<tr>
									<td><img id="show-img" class="{{$topic->image_id}}" src="{{ URL::asset('images/img_trans.gif') }}" /></td>
									<td>{{$topic->order_no}}</td>
									<td>{{$topic->id}}</td>
									<td>{{$topic->title}}</td>
									<td>{{$topic->short_name}}</td>
									<td class="text-center">

											<!-- <a class="btn btn-primary" href="{{ route('topic_views.show', $topic->id) }}">View</a> -->
											<a class="btn btn-warning" href="{{ route('topic_views.edit', $topic->id) }}">Edit</a>
											<form action="{{ route('topic_views.destroy', $topic->id) }}"
												method="POST" style="display: inline;"
												onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
												<input type="hidden" name="_method" value="DELETE">
												<input type="hidden" name="_token" value="{{ csrf_token() }}">
												<input type="hidden" name="level_id" value="{{ $level_id }}">
												<input type="hidden" name="unit_id" value="{{ $unit_id }}">
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