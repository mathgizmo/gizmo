@extends('layouts.app')

@section('content')

	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">Student Detail</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-2">
								<div class="form-group">
									<label for="name">E-mail</label>
								</div>
							</div>
							<div class="col-md-10">
								<p class="form-control-static"> {{ $student->email }}</p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-2">
								<div class="form-group">
									<label for="name">Name</label>
								</div>
							</div>
							<div class="col-md-10">
								<p class="form-control-static"> {{ $student->name }}</p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-2">
								<div class="form-group">
									<label for="name">Registration time</label>
								</div>
							</div>
							<div class="col-md-10">
								<p class="form-control-static"> {{ $student->created_at? $student->created_at->format('Y/m/d H:i') : '' }}</p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-2">
								<div class="form-group">
									<label for="name">Last action time</label>
								</div>
							</div>
							<div class="col-md-10">
								<p class="form-control-static"> {{ $student->students_tracking->last() != null ? date('H:i d.m.Y', strtotime($student->students_tracking->last()->date)) : 'Never' }}</p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="table-responsive">
									<table class="table table-striped">
										<thead>
											<tr>
												<th class="col-md">Lesson ID</th>
												<th class="col-md">Date</th>
												<th class="col-md">Action</th>
												<th class="col-md">Start_datetime</th>
												<th class="col-md">Weak questions</th>
											</tr>
										</thead>
										<tbody>
											@foreach($student->students_tracking as $students_tracking)
												<tr>
													<td>{{ $students_tracking->lesson_id }}</td>
													<td>{{ date('H:i d.m.Y', strtotime($students_tracking->date)) }}</td>
													<td>{{ $students_tracking->action }}</td>
													<td>{{ $students_tracking->start_datetime }}</td>
													<td>{{ $students_tracking->weak_questions }}</td>
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