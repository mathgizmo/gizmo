@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Units')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('units.index')  }}">Manage Units</a></li>
    <li class="breadcrumb-item active">Edit Unit</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Edit Unit</div>
        <form role="form" action="{{ route('units.update', $unit->id) }}" method="POST">
        <div class="card-body p-0">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group row mt-3 {{ $errors->has('level_id') ? ' has-error' : '' }}">
                    <label for="level_id" class="col-md-2 form-control-label ml-3 font-weight-bold">Level</label>

                    <div class="col-md-8">
                        <select class="form-control" name="level_id" id="level_id">

                            @if (count($levels) > 0)
                                <option value="">Select From ...</option>
                                @foreach($levels as $level)
                                    <option value="{{$level->id}}" @if (old("level_id") == $level->id) selected="selected" @endif  @if ($level->id == $unit->lid) selected="selected"
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
                    <label for="unit_title" class="col-md-2 form-control-label ml-3 font-weight-bold"> Unit Title</label>

                    <div class="col-md-8">
                        <textarea id="unit_title" class="form-control"  name="unit_title">{{$unit->title}}</textarea>

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
                        <label for="type" class="col-md-3"> <input {{ ($unit->dependency == true) ? 'checked="checked"' : ''}} type="checkbox" name="dependency" value="1"></label>
                        @if ($errors->has('dependency'))
                            <span class="form-text">
                                                <strong>{{ $errors->first('dependency') }}</strong>
                                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row{{ $errors->has('dev_mode') ? ' has-error' : '' }}">
                    <label for="type" class="col-md-2 form-control-label ml-3 font-weight-bold">Level in development</label>
                    <div class="col-md-8 radio">
                        <label for="type" class="col-md-3"> <input {{ ($unit->dev_mode == true) ? 'checked="checked"' : ''}} type="checkbox" name="dev_mode" value="1"></label>
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
                                    <option <?php echo ($count == $unit->order_no) ? 'selected="selected"' : ''; ?> value="{{$count}}">{{$count}}</option>
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
                <a class="btn btn-secondary" href="{{ route('units.create') }}">Back</a>
                <button class="btn btn-dark" type="submit" >Update</button>
            </div>
        </form>
    </div>
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
