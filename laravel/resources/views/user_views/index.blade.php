@extends('layouts.app')

@section('content')

	<div class="container">
		@if(session('status'))
			<div class="alert alert-success">
				{{ session('status') }}
			</div>
		@endif
		<div class="panel panel-default">
			<div class="panel-heading">Users</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-striped">
								<thead>
								<tr>
									<th class="col-md">ID</th>
									<th class="col-md">Name</th>
									<th class="col-md">Email</th>
									<th class="col-md"></th>
								</tr>
								</thead>
								<tbody>
								@foreach($users as $user)
									<tr>
										<td>{{ $user->id }}</td>
										<td>{{ $user->name }}</td>
										<td>{{ $user->email }}</td>
										<td>
											<div class="btn-group">
												<a href="users/{{ $user->id }}/edit" class="btn btn-info">Edit</a>
												<form method="POST" action="users/{{ $user->id }}">
													{{ csrf_field() }}
													<input type="hidden" name="_method" value="delete">
													<button type="button" class="btn btn-danger">Delete</button>
												</form>
											</div>
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

@endsection

@section('scripts')
	<script>
		$(document).ready(function() {
		   	$('.btn-danger').on('click', function(event) {
		   	   event.preventDefault();
		   	   var _delete = confirm("Do you really want to delete this user?");

		   	   if (_delete) {
		   	       $(this).closest('form').trigger('submit');
			   }
			});
		});
	</script>
@endsection