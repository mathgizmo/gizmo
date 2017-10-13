@extends('layouts.app')

@section('content')
 
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><div class="panel-title">
             Edit Topic
			</div></div>

                <div class="panel-body">

    <div class="row">
        <div class="col-md-12">

            <form id="topic" class="form-horizontal" role="form" action="{{ route('topic_views.update', $topic->id) }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

		        <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                    <label for="level_id" class="col-md-4 control-label">Level</label>

                    <div class="col-md-6">
					      <select class="form-control" name="level_id" id="level_id">
						  
						  @if (count($levels) > 0)
							  <option value="">Select From ...</option>
								@foreach($levels as $level)
									<option value="{{$level->id}}" @if (old("level_id") == $level->id) selected="selected" @endif  @if ( $level->id == $topic->lid) selected="selected" 
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
										<option value="{{$unit->id}}" @if ( $unit->id == $topic->uid) selected="selected" 
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
                        <label for="topic_title" class="col-md-4 control-label"> Title</label>

                        <div class="col-md-6">
                            <input id="topic_title" type="text" class="form-control"  name="topic_title" placeholder="Enter Topic text.." value="{{$topic->title}}">
                         
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
                                <input id="short_name" type="text" class="form-control"  name="short_name" placeholder="Enter Short Name" value="{{ $topic->short_name }}">
                             
                                @if ($errors->has('short_name'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('short_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                      </div>
					  
				<div class="form-group{{ $errors->has('dependency') ? ' has-error' : '' }}">
					<label for="type" class="col-md-4 control-label">Dependency</label>

                        <div class="col-md-6 radio"> 
							<label for="type" class="col-md-3"> <input type="radio" name="dependency" <?php echo ( "Yes" == $topic->dependency) ? 'checked="checked"' : ''; ?> value="Yes">Yes</label>
							<label for="type" class="col-md-3"> <input type="radio" name="dependency" <?php echo ( "Yes" == $topic->dependency) ? 'checked="checked"' : ''; ?> value="No"> No</label>
							

                            @if ($errors->has('dependency'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('dependency') }}</strong>
                                </span>
                            @endif
                        </div>
					</div>

                    <div class="form-group{{ $errors->has('image_id') ? ' has-error' : '' }}">
                        <label for="image_id" class="col-md-4 control-label">Image</label>

                        <div class="col-md-6"> 
                            <label id="change-image"><img id="show-img" class="{{$topic->image_id}}" src="{{ URL::asset('images/img_trans.gif') }}" /><a href="#" class="btn" data-toggle="modal" data-target="#addImageModal">Change Image</a></label>
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
                                        <option <?php echo ($count == $topic->order_no) ? 'selected="selected"' : ''; ?> value="{{$count}}">{{$count}}</option>
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
                            <a class="btn btn-default" href="{{ route('topic_views.create') }}">Back</a>
                            <button class="btn btn-primary" type="submit" >Update</button>
			
            			</div>
            		</div>
                </form>
                </div>
            </div>
	
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
