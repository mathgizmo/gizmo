@extends('layouts.app')

@section('content')

<div class="container">

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Custom Search</h3>
		</div>
		<div class="panel-body">
				<div class="row">
				<div class="col-md-12">
					<form class="form-horizontal" role="form" action="{{ route('question_views.index') }}" method="GET">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

				        <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                            <label for="level_id" class="col-md-4 control-label">Level</label>

                            <div class="col-md-6">
                                <select class="form-control" name="level_id" id="level_id">

                                    @if (count($levels) > 0)
                                        <option value="">Select From ...</option>
                                        @foreach($levels as $level)
                                            <option value="{{$level->id}}" <?php echo ($level->id == $level_id) ? 'selected' : ''; ?>
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
										<option value="">Select From ...</option>
								@if (count($units) > 0)

									@foreach($units as $unit)
											<option value="{{$unit->id}}" <?php echo ($unit->id == $unit_id) ? 'selected' : ''; ?>
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
									<option value="">Select From ...</option>
								@if (count($topics) > 0)
										@foreach($topics as $topic)
											<option value="{{$topic->id}}" <?php echo ($topic->id == $topic_id) ? 'selected' : ''; ?>
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

						<div class="form-group{{ $errors->has('lesson_id') ? ' has-error' : '' }}">
                            <label for="lesson_id" class="col-md-4 control-label">Lesson</label>

                            <div class="col-md-6">
							      <select class="form-control" name="lesson_id" id="lesson_id">
									<option value="">Select From ...</option>
									@if (count($lessons) > 0)
								  @foreach($lessons as $lesson)
											<option value="{{$lesson->id}}" <?php echo ($lesson->id == $lesson_id) ? 'selected' : ''; ?>
											>{{$lesson->title}}</option>
										@endforeach
								  @endif
									</select>

                                @if ($errors->has('lesson_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lesson_id') }}</strong>
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
						</div>
					</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">Question / Show </div>

		<div class="panel-body">

			<div class="row">
				<div class="col-md-12">
					{{ $questions->links() }}
				</div>
			</div>

			<div class="row">
			<div class="col-md-12">
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
                                <th class="col-md">Actions</th>
								<th class="col-md">Level</th>
								<th class="col-md">Unit</th>
								<th class="col-md">Topic</th>
								<th class="col-md">Lesson</th>
								<th class="col-md">Question</th>
								<th class="col-md">Type</th>
								<th class="col-md">ReplyMode</th>
							</tr>
						</thead>


						<tbody>
							@foreach($questions as $question)
								<tr>
                                    <td class="text-right">

                                            <a class="btn btn-primary" href="{{ route('question_views.show', $question->id) }}">View</a>
                                            <a class="btn btn-warning" href="{{ route('question_views.edit', $question->id) }}">Edit</a>
                                            <form action="{{ route('question_views.destroy', $question->id) }}"
                                                method="POST" style="display: inline;"
                                                onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button class="btn btn-danger" type="submit">Delete</button>
                                                </form>
                                    </td>
									<td>{{$question->ltitle}}</td>
									<td>{{$question->utitle}}</td>
									<td>{{$question->ttitle}}</td>
									<td>{{$question->title}}</td>
									<td>{{ $question->question }}</td>
									<td>{{$question->type}}</td>
									<td>{{$question->reply_mode}}</td>
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