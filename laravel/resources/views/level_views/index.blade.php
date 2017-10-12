@extends('layouts.app')

@section('content')

<div class="container">
	<div class="panel panel-default">
		<div class="panel-heading clearfix"> 
			<div class="panel-title pull-left">
				Levels List
			</div>
			<div class="pull-right">
				<a class="btn btn-primary" href="{{ route('level_views.create') }}">Create</a>

			</div>
		</div>

		<div class="panel-body">

			@if(Session::has('message'))
			<div id="successMessage" class="alert alert-success">
				<span class="glyphicon glyphicon-ok"></span>
				<em> {!! session('message') !!}</em>
			</div>
			@endif

			<div class="row">
				<div class="col-md-12">
				
					<div class="row">
						<div class="col.md.12">
							<div class="table-responsive">
								<table class="table table-striped">
									<thead>
										<tr>
											<th class="col-md">ID</th>
											<th class="col-md">Order No</th>
											<th class="col-md">Title</th>
											<th class="col-md-3">OPTIONS</th>
										</tr>
									</thead>


									<tbody>
										@foreach($levels as $level)
										<tr>
											<td>{{$level->id}}</td>
											<td>{{$level->order_no}}</td>
											<td>{{$level->title}}</td>
											<td class="text-right">

												<!-- <a class="btn btn-primary" href="{{ route('level_views.show', $level->id) }}">View</a> -->
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