@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Topic / Create </div>
			 	<div class="panel-body">
			 		@if(Session::has('flash_message'))
					<div id="successMessage" class="alert alert-success">
						<span class="glyphicon glyphicon-ok"></span>
						<em> {!! session('flash_message') !!}</em>
					</div>
					@endif
					<form id="topic" class="form-horizontal create-topic" role="form" action="{{ route('topic_views.store') }}" method="POST">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

				        <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                            <label for="level_id" class="col-md-4 control-label">Level</label>

                            <div class="col-md-6">
						      	<select class="form-control" name="level_id" id="level_id">
								  @if (count($levels) > 0)
								  	<option value="">Select From ...</option>
									@foreach($levels as $level)
									<option value="{{$level->id}}"
									@if (old("level_id") == $level->id) selected="selected"
									@endif
									@if ( $level->id == $lid)
										selected="selected"
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

						<div id="unit_options"> </div>
						<div class="form-group{{ $errors->has('unit_id') ? ' has-error' : '' }}">
                            <label for="unit_id" class="col-md-4 control-label">Unit</label>

                            <div class="col-md-6">
							      <select class="form-control" name="unit_id" id="unit_id">
								   @if (count($units) > 0)
								  @foreach($units as $unit)
											<option value="{{$unit->id}}" @if ( $unit->id == $uid) selected="selected"
											@endif
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

						<div class="form-group{{ $errors->has('topic_title') ? ' has-error' : '' }}">
                            <label for="topic_title" class="col-md-4 control-label">Topic Title</label>

                            <div class="col-md-6">
                            	<input id="topic_title" type="text" class="form-control"  name="topic_title" placeholder="Enter Topic text.." value="{{ old('topic_title') }}">

                                @if ($errors->has('topic_title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('topic_title') }}</strong>
                                    </span>
                                @endif
                            </div>
				      </div>

				      <div class="form-group{{ $errors->has('short_name') ? ' has-error' : '' }}">
                            <label for="short_name" class="col-md-4 control-label">Short Name</label>

                            <div class="col-md-6">
                            	<input id="short_name" type="text" class="form-control"  name="short_name" placeholder="Enter Short Name" value="{{ old('short_name') }}">

                                @if ($errors->has('short_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('short_name') }}</strong>
                                    </span>
                                @endif
                            </div>
				      </div>

					<div class="form-group{{ $errors->has('image_id') ? ' has-error' : '' }}">
	                    <label for="image_id" class="col-md-4 control-label">Image</label>

	                    <div class="col-md-6">
							<label id="add-image"><a href="#" class="btn" data-toggle="modal" data-target="#addImageModal">Add Image</a></label>
							<label id="change-image"><img id="show-img" class="cb0-img" src="{{ URL::asset('images/img_trans.gif') }}" /><a href="#" class="btn" data-toggle="modal" data-target="#addImageModal">Change Image</a></label>
							<input type="hidden" name="image_id" value="">
	                    </div>
					</div>

					<div class="form-group{{ $errors->has('order_no') ? ' has-error' : '' }}">
                        <label for="order_no" class="col-md-4 control-label">Order No</label>

                        <div class="col-md-6">
						      <select class="form-control" name="order_no" id="order_no">
								<option value="1">1</option>
							    @if ($total_topic > 0)
								  	@for($count = 2; $count <= $total_topic + 1; $count++)
										<option <?php echo ($count > $total_topic) ? 'selected="selected"' : ''; ?> value="{{$count}}">{{$count}}</option>
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
			               <a class="btn btn-default" href="{{ route('topic_views.index') }}">Back</a>
			                  <button class="btn btn-primary" type="submit" >Submit</button>
					     </div>
					</div>
            </form>
			@if (count($topics) > 0)
			<div class="row">
				<div class="col.md.12">
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

<!-- Modal -->
<div class="modal fade" id="addImageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  	<div class="modal-dialog" role="document">
	    <div class="modal-content">
	      	<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        	<h4 class="modal-title" id="myModalLabel">Add Topic Image</h4>
	      	</div>
	      	<div class="modal-body">
	        	<div class="topic-images">
	        		<ul>
					  	<li><input type="checkbox" id="cb1" value="cb1-img" />
						    <label for="cb1"><img id="cb1-img" src="{{ URL::asset('images/img_trans.gif') }}" /></label>
						</li>
						<li><input type="checkbox" id="cb2" value="cb2-img" />
						    <label for="cb2"><img id="cb2-img" src="{{ URL::asset('images/img_trans.gif') }}" /></label>
						</li>
						<li><input type="checkbox" id="cb3" value="cb3-img" />
						    <label for="cb3"><img id="cb3-img" src="{{ URL::asset('images/img_trans.gif') }}" /></label>
						</li>
						<li><input type="checkbox" id="cb4" value="cb4-img" />
						    <label for="cb4"><img id="cb4-img" src="{{ URL::asset('images/img_trans.gif') }}" /></label>
						</li>
					</ul>
	        	</div>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        	<button id="save-image" type="button" class="btn btn-primary">Add Image</button>
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
