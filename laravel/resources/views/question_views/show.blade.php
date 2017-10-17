@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">Question Details</div>

					<div class="panel-body">

						<div class="row bg-success">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">Level</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->ltitle}}</p>
							</div>
						</div>
						<div class="row bg-info">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">Unit</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->utitle}}</p>
							</div>
						</div>
						<div class="row bg-success">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">Topic</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->ttitle}}</p>
							</div>
						</div>
						<div class="row bg-info">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">Lesson</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->title}}</p>
							</div>
						</div>
						<div class="row bg-success">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">Question</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->question}}</p>
							</div>
						</div>
						<div class="row bg-success">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">Type</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->type}}</p>
							</div>
						</div>
						<div class="row bg-info">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">ReplyMode</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->reply_mode}}</p>
							</div>
						</div>
						<div class="row bg-success">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">Shape</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->shape}}</p>
							</div>
						</div>
						<div class="row bg-info">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">MinValue</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->min_value}}</p>
							</div>
						</div>
						<div class="row bg-success">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">MaxValue</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->max_value}}</p>
							</div>
						</div>
						<div class="row bg-info">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">IniPosition</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->initial_position}}</p>
							</div>
						</div>
						<div class="row bg-success">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">StepValue</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->step_value}}</p>
							</div>
						</div>
						<div class="row bg-info">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">Image</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->image}}</p>
							</div>
						</div>
						<div class="row bg-success">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">Answer</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->answer}}</p>
							</div>
						</div>
						<div class="row bg-info">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">Feedback</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->feedback}}</p>
							</div>
						</div>

						<div class="row bg-success">
							<div class="col-md-2">
							<div class="form-group">
								<label for="name">Explanation</label>
							</div>
							</div>

							<div class="col-md-10">
									<p class="form-control-static"> {{$question->explanation}}</p>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12 text-right">
									<a class="btn btn-default" href="{{ route('question_views.index') }}">Back</a>
									<a class="btn btn-warning" href="{{ route('question_views.edit', $question->id) }}">Edit</a>
									<form action="{{ route('question_views.destroy', $question->id) }}"
										method="POST" style="display: inline;"
										onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
										<input type="hidden" name="_method" value="DELETE">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<button class="btn btn-danger" type="submit">Delete</button>
									</form>
							</div>
						</div>

				</div>
			</div>
		</div>
	</div>
</div>


@endsection