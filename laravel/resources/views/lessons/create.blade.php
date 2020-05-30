@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Lessons')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('lessons.index')  }}">Manage Lessons</a></li>
    <li class="breadcrumb-item active">Create Lesson</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Create Lesson</div>
        <form class="form-horizontal" role="form" action="{{ route('lessons.store') }}" method="POST">
        <div class="card-body p-0">
            @if(Session::has('flash_message'))
                <div id="successMessage" class="alert alert-success mt-3">
                    <span class="glyphicon glyphicon-ok"></span>
                    <em> {!! session('flash_message') !!}</em>
                </div>
            @endif
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group row mt-3 {{ $errors->has('level_id') ? ' has-error' : '' }}">
                    <label for="level_id" class="col-md-2 form-control-label ml-3 font-weight-bold">Level</label>

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

                <div id="unit_options"> </div>
                <div class="form-group row{{ $errors->has('unit_id') ? ' has-error' : '' }}">
                    <label for="unit_id" class="col-md-2 form-control-label ml-3 font-weight-bold">Unit</label>

                    <div class="col-md-8">
                        <select class="form-control" name="unit_id" id="unit_id">
                            @if (count($units) > 0)
                                @foreach($units as $unit)
                                    <option value="{{$unit->id}}" @if ($unit->id == $uid) selected="selected"
                                            @endif
                                    >{{$unit->title}}</option>
                                @endforeach
                            @endif
                        </select>

                        @if ($errors->has('unit_id'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('unit_id') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('topic_id') ? ' has-error' : '' }}">
                    <label for="topic_id" class="col-md-2 form-control-label ml-3 font-weight-bold">Topic</label>

                    <div class="col-md-8">
                        <select class="form-control" name="topic_id" id="topic_id">

                            @if (count($topics) > 0)
                                @foreach($topics as $topic)
                                    <option value="{{$topic->id}}" @if ($topic->id == $tid) selected="selected"
                                            @endif
                                    >{{$topic->title}}</option>
                                @endforeach
                            @endif

                        </select>

                        @if ($errors->has('topic_id'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('topic_id') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('lesson_title') ? ' has-error' : '' }}">
                    <label for="lesson_title" class="col-md-2 form-control-label ml-3 font-weight-bold">Lesson Title</label>

                    <div class="col-md-8">
                        <input type="text" id="lesson_title" class="form-control"  name="lesson_title" placeholder="Enter question text.." value="{{ old('lesson_title') }}">

                        @if ($errors->has('lesson_title'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('lesson_title') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('randomisation') ? ' has-error' : '' }}">
                    <label for="randomisation" class="col-md-2 form-control-label ml-3 font-weight-bold">Turn on randomisation</label>
                    <div class="col-md-8 radio">
                        <label for="randomisation" class="col-md-3"> <input checked="checked" type="checkbox" name="randomisation" value="1"></label>
                        @if ($errors->has('randomisation'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('randomisation') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('dependency') ? ' has-error' : '' }}">
                    <label for="dependency" class="col-md-2 form-control-label ml-3 font-weight-bold">This should be finished to continue</label>
                    <div class="col-md-8 radio">
                        <label for="dependency" class="col-md-3"> <input checked="checked" type="checkbox" name="dependency" value="1"></label>
                        @if ($errors->has('dependency'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('dependency') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('challenge') ? ' has-error' : '' }}">
                    <label for="challenge" class="col-md-2 form-control-label ml-3 font-weight-bold">This is challenge</label>
                    <div class="col-md-8 radio">
                        <label for="challenge" class="col-md-3">
                            <input type="checkbox" name="challenge" value="1"></label>
                        @if ($errors->has('challenge'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('challenge') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('order_no') ? ' has-error' : '' }}">
                    <label for="order_no" class="col-md-2 form-control-label ml-3 font-weight-bold">Order No</label>

                    <div class="col-md-8">
                        <select class="form-control" name="order_no" id="order_no">
                            <option value="1">1</option>
                            @if ($total_lesson > 0)
                                @for($count = 2; $count <= $total_lesson + 1; $count++)
                                    <option <?php echo ($count > $total_lesson) ? 'selected="selected"' : ''; ?> value="{{$count}}">{{$count}}</option>
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
                <div class="form-group row{{ $errors->has('dev_mode') ? ' has-error' : '' }}">
                    <label for="type" class="col-md-2 form-control-label ml-3 font-weight-bold">Lesson in development</label>

                    <div class="col-md-8 radio">
                        <label for="type" class="col-md-3"> <input checked="checked" type="checkbox" name="dev_mode" value="1"></label>
                        @if ($errors->has('dev_mode'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('dev_mode') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
        </div>
            <div class="card-footer">
                <a class="btn btn-secondary" href="{{ route('lessons.index') }}">Back</a>
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
