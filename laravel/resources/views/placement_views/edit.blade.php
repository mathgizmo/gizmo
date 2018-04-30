@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><div class="panel-title">
                    Edit Placement
                </div></div>

                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-12">

                        <form class="form-horizontal" role="form" action="{{ route('placement_views.update', $placement->id) }}" method="POST">
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group{{ $errors->has('unit_id') ? ' has-error' : '' }}">
                                <label for="unit_id" class="col-md-4 control-label">Unit</label>
                                <div class="col-md-6">
                                    <select class="form-control" name="unit_id" id="unit_id">
                                      @if (count($units) > 0)
                                        <option value="">Select From ...</option>
                                            @foreach($units as $unit)
                                                <option value="{{$unit->id}}" @if (old("unit_id") == $unit->id) selected="selected" @endif 
                                                @if ( $unit->id == $placement->unit_id) selected="selected" 
                                                @endif 
                                                >{{$unit->title}}
                                            @endforeach
                                         </option>
                                     @endif
                                    </select>
                                    @if ($errors->has('unit_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('unit_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
                                <label for="question" class="col-md-4 control-label">Question</label>
                                <div class="col-md-6">
                                    <textarea id="question" class="form-control"  name="question"> {{$placement->question}}</textarea>
                                    @if ($errors->has('question'))
                                    <span class="help-block"> 
                                        <strong>{{ $errors->first('question') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('is_active') ? ' has-error' : '' }}">
                                <label for="type" class="col-md-4 control-label">
                                    Is Active
                                </label>

                                <div class="col-md-6 radio">
                                    <label for="type" class="col-md-3"> <input {{ ($placement->is_active == 1) ? 'checked="checked"' : ''}} type="checkbox" name="is_active" value="1"></label>
                                    @if ($errors->has('is_active'))
                                        <span class="help-block">
												<strong>{{ $errors->first('is_active') }}</strong>
											</span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('order') ? ' has-error' : '' }}">
                                <label for="order" class="col-md-4 control-label">Order No</label>
                                <div class="col-md-6">
                                    <select class="form-control" name="order" id="order">
                                        <option value="1">1</option>
                                        @if ($total_placements > 0)
                                        @for($count = 2; $count <= $total_placements + 1; $count++)
                                        <option <?php echo ($count == $placement->order) ? 'selected="selected"' : ''; ?> value="{{$count}}">{{$count}}</option>
                                        @endfor
                                        @endif
                                    </select>
                                    @if ($errors->has('order'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('order') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <a class="btn btn-default" href="{{ route('placement_views.index') }}">Back</a>
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

@endsection
