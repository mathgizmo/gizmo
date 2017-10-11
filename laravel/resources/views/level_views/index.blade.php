@extends('layouts.app')

@section('content')

<div class="container">
	<div class="panel panel-default">
		<div class="panel-heading clearfix"> 
			<div class="panel-title pull-left">
             Search and Create!
			</div>
			<div class="pull-right">
				<a class="btn btn-primary" href="{{ route('level_views.index') }}">Create</a>

			</div>
	</div>

		<div class="panel-body">
		
		@if(Session::has('flash_message'))
					<div id="successMessage" class="alert alert-success">
						<span class="glyphicon glyphicon-ok"></span>
							<em> {!! session('flash_message') !!}</em>
					</div>
				@endif
		
			<div class="row">
				<div class="col-md-12">
					<form class="form-horizontal" role="form" action="{{ route('level_views.store') }}" method="POST">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="form-group{{ $errors->has('level_title') ? ' has-error' : '' }}">
                            <label for="level_title" class="col-md-4 control-label">Level Title</label>

                            <div class="col-md-6">
                                <input type="text" id="level_title" class="form-control" name="level_title"  value="{{ old('level_title') }}" placeholder="Enter Level Title text.."/>

                                @if ($errors->has('level_title'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('level_title') }}</strong>
                                    </span>
                                @endif
                            </div>
				      </div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button class="btn float-right btn-primary" type="submit" >Create</button>
							</div>
						</div>
						</form>
						
						<div class="row">
							<div class="col.md.12">
								<div class="table-responsive">
									<table class="table table-striped">
										<thead>
											<tr>
												<th class="col-md">Level ID</th>
												<th class="col-md">Level Title</th>
												<th class="col-md-3">OPTIONS</th>
											</tr>
										</thead>


						<tbody>
							@foreach($levels as $level)
								<tr>
									<td>{{$level->id}}</td>
									<td>{{$level->title}}</td>
									<td class="text-right">

											<a class="btn btn-primary" href="{{ route('level_views.show', $level->id) }}">View</a>
											<a class="btn btn-warning" href="{{ route('level_views.edit', $level->id) }}">Edit</a>
											<form action="{{ route('level_views.destroy', $level->id) }}" 
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

@section('scripts')

<script>
$(document).ready(function(){
        setTimeout(function() {
          $('#successMessage').fadeOut('fast');
        }, 4000); // <-- time in milliseconds
    });
</script>

@endsection