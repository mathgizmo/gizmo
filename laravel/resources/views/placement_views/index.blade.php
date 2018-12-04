@extends('layouts.app')

@section('content')

<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <div class="panel-title pull-left">
                Placements List
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('placement_views.create') }}">Create</a>
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

                    <div class="row">
                        <div class="col.md.12">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="col-md">
                                                ID
                                                <a href="{{ route('placement_views.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : 'desc')])) }}">
                                                    <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                                </a>
                                            </th>
                                            <th class="col-md">
                                                Order No
                                                <a href="{{ route('placement_views.index', array_merge(request()->all(), ['sort' => 'order', 'order' => ((request()->sort == 'order' && request()->order == 'desc') ? 'asc' : 'desc')])) }}">
                                                    <i class="fa fa-fw fa-sort{{ (request()->sort == 'order' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'order' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                                </a>
                                            </th>
                                            <th class="col-md">
                                                Question
                                                <a href="{{ route('placement_views.index', array_merge(request()->all(), ['sort' => 'question', 'order' => ((request()->sort == 'question' && request()->order == 'desc') ? 'asc' : 'desc')])) }}">
                                                    <i class="fa fa-fw fa-sort{{ (request()->sort == 'question' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'question' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                                </a>
                                            </th>
                                            <th class="col-md">
                                                Unit
                                                </a>
                                            </th>
                                            <th class="col-md">
                                                Active
                                                <a href="{{ route('placement_views.index', array_merge(request()->all(), ['sort' => 'is_active', 'order' => ((request()->sort == 'is_active' && request()->order == 'desc') ? 'asc' : 'desc')])) }}">
                                                    <i class="fa fa-fw fa-sort{{ (request()->sort == 'is_active' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'is_active' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                                </a>
                                            </th>
                                            <th class="col-md-3">
                                                OPTIONS
                                            </th>
                                        </tr>
                                    </thead>


                                    <tbody>
                                        @foreach($placements as $placement)
                                        <tr>
                                            <td>{{$placement->id}}</td>
                                            <td>{{$placement->order}}</td>
                                            <td>{{$placement->question}}</td>
                                            <td>{{$placement->unit->title}}</td>
                                            <td>{{($placement->is_active == 1) ? 'Yes' : 'No'}}</td>
                                            <td class="text-right">
                                                <a class="btn btn-warning" href="{{ route('placement_views.edit', $placement->id) }}">Edit</a>
                                                <form action="{{ route('placement_views.destroy', $placement->id) }}"
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
                </div>
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
