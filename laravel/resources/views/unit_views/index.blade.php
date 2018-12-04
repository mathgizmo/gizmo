@extends('layouts.app')

@section('content')

<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <div class="panel-title pull-left">
                Search and Create!
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('unit_views.create') }}">Create</a>

            </div>
        </div>

        <div class="panel-body">

            <div class="row">
                <div class="col-md-12">
                    <form class="form-horizontal" role="form" action="{{ route('unit_views.index') }}" method="GET">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                            <label for="level_id" class="col-md-4 control-label">Level</label>

                            <div class="col-md-6">
                                <select class="form-control" name="level_id" id="level_id">

                                    @if (count($levels) > 0)
                                    <option value="">Select From ...</option>
                                    @foreach($levels as $level)
                                    <option <?php echo ($level_id == $level->id) ? 'selected="selected"' : ''; ?> value="{{$level->id}}"
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

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button class="btn btn-primary" type="submit" >Search</button>
                                </div>
                            </div>
                        </form>

                        <div class="row">
                            @if(Session::has('message'))
                            <div id="successMessage" class="alert alert-success">
                                <span class="glyphicon glyphicon-ok"></span>
                                <em> {!! session('message') !!}</em>
                            </div>
                            @endif
                            <div class="col.md.12">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="col-md">
                                                    Unit ID
                                                    <a href="{{ route('unit_views.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : 'desc')])) }}">
                                                        <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                                    </a>
                                                </th>
                                                <th class="col-md">
                                                    Order No
                                                    <a href="{{ route('unit_views.index', array_merge(request()->all(), ['sort' => 'order_no', 'order' => ((request()->sort == 'order_no' && request()->order == 'desc') ? 'asc' : 'desc')])) }}">
                                                        <i class="fa fa-fw fa-sort{{ (request()->sort == 'order_no' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'order_no' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                                    </a>
                                                </th>
                                                <th class="col-md">
                                                    Unit Title
                                                    <a href="{{ route('unit_views.index', array_merge(request()->all(), ['sort' => 'title', 'order' => ((request()->sort == 'title' && request()->order == 'desc') ? 'asc' : 'desc')])) }}">
                                                        <i class="fa fa-fw fa-sort{{ (request()->sort == 'title' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'title' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                                    </a>
                                                </th>
                                                <th class="col-md">
                                                    Dependency
                                                    <a href="{{ route('unit_views.index', array_merge(request()->all(), ['sort' => 'dependency', 'order' => ((request()->sort == 'dependency' && request()->order == 'desc') ? 'asc' : 'desc')])) }}">
                                                        <i class="fa fa-fw fa-sort{{ (request()->sort == 'dependency' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'dependency' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                                    </a>
                                                </th>
                                                <th class="col-md-3">
                                                    OPTIONS
                                                </th>
                                            </tr>
                                        </thead>


                                        <tbody>
                                            @foreach($units as $unit)
                                            <tr>
                                                <td>{{$unit->id}}</td>
                                                <td>{{$unit->order_no}}</td>
                                                <td>{{$unit->title}}</td>
                                                <td>{{($unit->dependency == true) ? 'Yes' : 'No'}}</td>
                                                <td class="text-right">

                                                    <!-- <a class="btn btn-primary" href="{{ route('unit_views.show', $unit->id) }}">View</a> -->
                                                    <a class="btn btn-warning" href="{{ route('unit_views.edit', $unit->id) }}">Edit</a>
                                                    <form action="{{ route('unit_views.destroy', $unit->id) }}"
                                                        method="POST" style="display: inline;"
                                                        onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <input type="hidden" name="level_id" value="{{ $level_id }}">
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
                    </div>
                </div>
            </div>
        </div>
    </div>


    @endsection
