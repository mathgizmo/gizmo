@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Level Create </div>
                 <div class="panel-body">
                    <form class="form-horizontal" role="form" action="{{ route('level_views.store') }}" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label for="title" class="col-md-4 control-label">Title</label>

                            <div class="col-md-6">
                                <textarea id="title" class="form-control"  name="title" placeholder="Enter Level Title"> {{ old('title') }}</textarea>

                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                          </div>

                        <div class="form-group{{ $errors->has('order_no') ? ' has-error' : '' }}">
                            <label for="order_no" class="col-md-4 control-label">Order No</label>

                            <div class="col-md-6">
                                  <select class="form-control" name="order_no" id="order_no">
                                    <option value="1">1</option>
                                    @if ($total_level > 0)
                                          @for($count = 2; $count <= $total_level + 1; $count++)
                                            <option <?php echo ($count > $total_level) ? 'selected="selected"' : ''; ?> value="{{$count}}">{{$count}}</option>
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
                        <div class="form-group{{ $errors->has('dependency') ? ' has-error' : '' }}">
                            <label for="type" class="col-md-4 control-label">This should be finished to continue</label>

                            <div class="col-md-6 radio">
                                <label for="type" class="col-md-3"> <input checked="checked" type="checkbox" name="dependency" value="1"></label>
                                @if ($errors->has('dependency'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('dependency') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                               <a class="btn btn-default" href="{{ route('level_views.index') }}">Back</a>
                                  <button class="btn btn-primary" type="submit" >Submit</button>
                             </div>
                        </div>
            </form>
              </div>
            </div>
        </div>
    </div>
</div>

@endsection
