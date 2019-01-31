@extends('layouts.app')

@section('content')

<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <div class="panel-title pull-left">
             Search and Create!
            </div>
            <div class="pull-right">
                    <a class="btn btn-primary" href="{{ route('lesson_views.create') }}">Create</a>
            </div>
    </div>

        <div class="panel-body">

            @if(Session::has('message'))
            <div id="successMessage" class="alert alert-success">
                <span class="glyphicon glyphicon-ok"></span>
                <em> {!! session('message') !!}</em>
            </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <form class="form-horizontal" role="form" action="{{ route('lesson_views.index') }}" method="GET">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                            <label for="level_id" class="col-md-4 control-label">Level</label>

                            <div class="col-md-6">
                                  <select class="form-control" name="level_id" id="level_id">

                                  @if (count($levels) > 0)
                                      <option value="">Select From ...</option>
                                        @foreach($levels as $level)
                                            <option value="{{$level->id}}"
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
                                    <option value="">Select From ...</option>
                                    @foreach($units as $unit)
                                            <option value="{{$unit->id}}"
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
                                @if (count($topics) > 0)
                                        <option value="">Select From ...</option>
                                        @foreach($topics as $topic)
                                            <option value="{{$topic->id}}"
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
            <div class="panel-heading">Lesson / Show </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        {{ $lessons->links() }}
                    </div>
                </div>
                <div class="row">
                    <div class="col.md.12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="col-md">
                                            ID
                                            <a href="{{ route('lesson_views.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                            </a>
                                        </th>
                                        <th class="col-md">
                                            Order No
                                            <a href="{{ route('lesson_views.index', array_merge(request()->all(), ['sort' => 'order_no', 'order' => ((request()->sort == 'order_no' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'order_no' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'order_no' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'order_no' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                            </a>
                                        </th>
                                        <th class="col-md">
                                            Title
                                            <a href="{{ route('lesson_views.index', array_merge(request()->all(), ['sort' => 'title', 'order' => ((request()->sort == 'title' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'title' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'title' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'title' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                            </a>
                                        </th>
                                        <th class="col-md">
                                            Randomisation
                                            <a href="{{ route('lesson_views.index', array_merge(request()->all(), ['sort' => 'randomisation', 'order' => ((request()->sort == 'randomisation' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'randomisation' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'randomisation' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'randomisation' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                            </a>
                                        </th>
                                        <th class="col-md">
                                            Dependency
                                            <a href="{{ route('lesson_views.index', array_merge(request()->all(), ['sort' => 'dependency', 'order' => ((request()->sort == 'dependency' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'dependency' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'dependency' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'dependency' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                            </a>
                                        </th>
                                        <th class="col-md">
                                            Lesson in development
                                            <a href="{{ route('lesson_views.index', array_merge(request()->all(), ['sort' => 'dev_mode', 'order' => ((request()->sort == 'dev_mode' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'dev_mode' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'dev_mode' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'dev_mode' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                            </a>
                                        </th>
                                        <th class="col-md-3">
                                            OPTIONS
                                        </th>
                                    </tr>
                                </thead>

                                <tbody> 
                                    <tr style="background: #999999;">
                                        <td>
                                            <input type="number" min="0" name="id" id="id-filter" style="width: 50px;">
                                        </td>
                                        <td>
                                            <input type="number"  min="0" name="order_no" id="order-filter" style="width: 60px;">
                                        </td>
                                        <td>
                                            <input type="text" name="title" id="title-filter" style="width: 100%;">
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">
                                            <a href="javascript:void(0);" onclick="filter()" class="btn btn-primary">Filter</a>
                                        </td>
                                    </tr>
                                    @foreach($lessons as $lesson)
                                        <tr>
                                            <td>{{$lesson->id}}</td>
                                            <td>{{$lesson->order_no}}</td>
                                            <td>{{$lesson->title}}</td>
                                            <td>{{($lesson->randomisation == true) ? 'Yes' : 'No'}}</td>
                                            <td>{{($lesson->dependency == true) ? 'Yes' : 'No'}}</td>
                                            <td>{{($lesson->dev_mode == true) ? 'Yes' : 'No'}}</td>
                                            <td class="text-right">
                                                <!-- <a class="btn btn-primary disabled" href="{{ route('lesson_views.show', $lesson->id) }}">View</a> -->
                                                <a class="btn btn-warning" href="{{ route('lesson_views.edit', $lesson->id) }}">Edit</a>
                                                <form action="{{ route('lesson_views.destroy', $lesson->id) }}"
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

            <div class="row">
                <div class="col-md-12">
                    {{ $lessons->links() }}
                </div>
            </div>

        </div>
    </div>
</div>
</div>

@endsection

@section('scripts')

<script type="text/javascript">
    $(document).ready(function(){
        setTimeout(function() {
            $('#successMessage').fadeOut('fast');
        }, 4000); // <-- time in milliseconds
    });
    function filter() {
        let url = new URL(window.location.href);
        const id = document.getElementById("id-filter").value;
        const order = document.getElementById("order-filter").value;
        const title = document.getElementById("title-filter").value;
        if(id) {
            url.searchParams.set('id', id);
        } else if (url.searchParams.get('id')) {
            url.searchParams.delete('id');
        }
        if(order) {
            url.searchParams.set('order_no', order);
        } else if (url.searchParams.get('order_no')) {
            url.searchParams.delete('order_no');
        }
        if(title) {
            url.searchParams.set('title', title);
        } else if (url.searchParams.get('title')) {
            url.searchParams.delete('title');
        }
        window.location.href = url.toString();
    } 
    function init() {
        const url = new URL(window.location.href);
        
        document.getElementById("id-filter").value = url.searchParams.get('id');
        document.getElementById("order-filter").value = url.searchParams.get('order_no');
        document.getElementById("title-filter").value = url.searchParams.get('title');

        document.querySelector('#level_id [value="' + url.searchParams.get('level_id') + '"]').selected = true;
        document.querySelector('#unit_id [value="' + url.searchParams.get('unit_id') + '"]').selected = true;
        document.querySelector('#topic_id [value="' + url.searchParams.get('topic_id') + '"]').selected = true;
    }
    window.onload = init;
</script>

@endsection