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
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Unit / Show </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        {{ $units->links() }}
                    </div>
                </div>
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
                                            <a href="{{ route('unit_views.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                            </a>
                                        </th>
                                        <th class="col-md">
                                            Order No
                                            <a href="{{ route('unit_views.index', array_merge(request()->all(), ['sort' => 'order_no', 'order' => ((request()->sort == 'order_no' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'order_no' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'order_no' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'order_no' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                            </a>
                                        </th>
                                        <th class="col-md">
                                            Unit Title
                                            <a href="{{ route('unit_views.index', array_merge(request()->all(), ['sort' => 'title', 'order' => ((request()->sort == 'title' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'title' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'title' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'title' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                            </a>
                                        </th>
                                        <th class="col-md">
                                            Dependency
                                            <a href="{{ route('unit_views.index', array_merge(request()->all(), ['sort' => 'dependency', 'order' => ((request()->sort == 'dependency' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'dependency' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'dependency' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'dependency' && request()->order == 'desc') ? '-desc' : '' }}"></i>
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
                                        <td class="text-right">
                                            <a href="javascript:void(0);" onclick="filter()" class="btn btn-primary">Filter</a>
                                        </td>
                                    </tr>
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
                <div class="row">
                    <div class="col-md-12">
                        {{ $units->links() }}
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
    }
    window.onload = init;
</script>
@endsection