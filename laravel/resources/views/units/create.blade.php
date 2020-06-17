@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Units')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('units.index')  }}">Manage Units</a></li>
    <li class="breadcrumb-item active">Create Unit</li>
@endsection

@section('content')
    @if(Session::has('flash_message'))
        <div id="successMessage" class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>
            <em> {!! session('flash_message') !!}</em>
        </div>
    @endif
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Create Unit</div>
        <form role="form" action="{{ route('units.store') }}" method="POST">
        <div class="card-body p-0">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group row mt-3 {{ $errors->has('level_id') ? ' has-error' : '' }}">
                    <label for="level_id" class="col-md-2 form-control-label ml-3 font-weight-bold">Module</label>

                    <div class="col-md-8">
                        <select class="form-control" name="level_id" id="level_id">
                            @if (count($levels) > 0)
                                <option value="">Select From ...</option>
                                @foreach($levels as $level)
                                    <option value="{{$level->id}}" @if (old("level_id") == $level->id) selected="selected" @endif  @if ($level->id == $lid) selected="selected"
                                            @endif
                                    >{{$level->title}}</option>
                                @endforeach
                            @endif
                        </select>

                        @if ($errors->has('level_id'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('level_id') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('unit_title') ? ' has-error' : '' }}">
                    <label for="unit_title" class="col-md-2 form-control-label ml-3 font-weight-bold">Unit Title</label>

                    <div class="col-md-8">
                        <textarea id="unit_title" class="form-control"  name="unit_title" placeholder="Enter Unit text.."> {{ old('unit_title') }}</textarea>

                        @if ($errors->has('unit_title'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('unit_title') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('dependency') ? ' has-error' : '' }}">
                    <label for="type" class="col-md-2 form-control-label ml-3 font-weight-bold">This should be finished to continue</label>

                    <div class="col-md-8 radio">
                        <label for="type" class="col-md-3"> <input checked="checked" type="checkbox" name="dependency" value="1"></label>
                        @if ($errors->has('dependency'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('dependency') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row{{ $errors->has('dev_mode') ? ' has-error' : '' }}">
                    <label for="type" class="col-md-2 form-control-label ml-3 font-weight-bold">Unit in development</label>
                    <div class="col-md-8 radio">
                        <label for="type" class="col-md-3"> <input checked="checked" type="checkbox" name="dev_mode" value="1"></label>
                        @if ($errors->has('dev_mode'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('dev_mode') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('order_no') ? ' has-error' : '' }}">
                    <label for="order_no" class="col-md-2 form-control-label ml-3 font-weight-bold">Order No</label>

                    <div class="col-md-8">
                        <select class="form-control" name="order_no" id="order_no">
                            <option value="1">1</option>
                            @if ($total_unit > 0)
                                @for($count = 2; $count <= $total_unit + 1; $count++)
                                    <option <?php echo ($count > $total_unit) ? 'selected="selected"' : ''; ?> value="{{$count}}">{{$count}}</option>
                                @endfor
                            @endif
                        </select>

                        @if ($errors->has('order_no'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('order_no') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
        </div>
            <div class="card-footer">
                <a class="btn btn-secondary" href="{{ route('units.index') }}">Back</a>
                <button class="btn btn-dark" type="submit" >Submit</button>
            </div>
        </form>
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

@section('styles')
    <style>
        @media screen and (max-width: 600px) {
            .col-md-8 {
                margin: 0 16px;
            }
        }
    </style>
@endsection
