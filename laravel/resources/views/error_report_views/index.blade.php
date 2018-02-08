@extends('layouts.app')

@section('content')

	<div class="container">
		<ul class="nav nav-pills nav-justified">
			<li class="{{ $type == 'new' ? 'active' : ''}}"><a href="{{ route('error_report.index', 'new') }}">New</a></li>
			<li class="{{ $type == 'decline' ? 'active' : ''}}"><a href="{{ route('error_report.index', 'decline') }}">Declined</a></li>
		</ul>
		<br>
		<div class="panel panel-default">
			<div class="panel-heading">Error report</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-striped">
								<thead>
								<tr>
									<th class="col-md">
										ID
									</th>
									<th class="col-md">
										Question ID
									</th>
									<th class="col-md">
										Answer ID
									</th>
									<th class="col-md">
										Student ID
									</th>
									<th class="col-md">
										Options
									</th>
									<th class="col-md">
										Comment
									</th>
									<th class="col-md">
										Time
									</th>
									<th class="col-md"></th>
								</tr>
								</thead>
								<tbody>
									@foreach($error_reports as $error_report)
										<tr>
											<td>{{ $error_report->id }}</td>
											<td><a href="{{ route('question_views.show', $error_report->question_id) }}" target="_blank">{{ $error_report->question_id }}</a></td>
											<td>{{ $error_report->answer_id }}</td>
											<td>{{ $error_report->student_id }}</td>
											<td>{{ $error_report->options }}</td>
											<td>{{ $error_report->comment }}</td>
											<td>{{ $error_report->created_at->format('d.m.Y H:i') }}</td>
											<td>
												<div class="btn-group">
													@if ($error_report->declined == 1)
														<a href="{{ route('error_report.update_status', ['type' => 'new', 'id' => $error_report->id]) }}" class="btn btn-info">Set new</a>
													@else
														<a href="{{ route('error_report.update_status', ['type' => 'decline', 'id' => $error_report->id]) }}" class="btn btn-info">Set decline</a>
													@endif
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