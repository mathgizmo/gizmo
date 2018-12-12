@extends('layouts.app')

@section('content')

<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <div class="panel-title pull-left">
                Levels List
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('level_views.create') }}">Create</a>

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
                                                <a href="{{ route('level_views.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                                    <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                                </a>
                                            </th>
                                            <th class="col-md">
                                                Order No
                                                <a href="{{ route('level_views.index', array_merge(request()->all(), ['sort' => 'order_no', 'order' => ((request()->sort == 'order_no' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'order_no' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                                    <i class="fa fa-fw fa-sort{{ (request()->sort == 'order_no' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'order_no' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                                </a>
                                            </th>
                                            <th class="col-md">
                                                Title
                                                <a href="{{ route('level_views.index', array_merge(request()->all(), ['sort' => 'title', 'order' => ((request()->sort == 'title' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'title' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                                    <i class="fa fa-fw fa-sort{{ (request()->sort == 'title' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'title' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                                </a>
                                            </th>
                                            <th class="col-md">
                                                Dependency
                                                <a href="{{ route('level_views.index', array_merge(request()->all(), ['sort' => 'dependency', 'order' => ((request()->sort == 'dependency' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'dependency' && request()->order == 'asc') ? '' : 'desc'))])) }}">
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

                                        @foreach($levels as $level)
                                        <tr>
                                            <td>{{$level->id}}</td>
                                            <td>{{$level->order_no}}</td>
                                            <td>{{$level->title}}</td>
                                            <td>{{($level->dependency == true) ? 'Yes' : 'No'}}</td>
                                            <td class="text-right">

                                                <!-- <a class="btn btn-primary" href="{{ route('level_views.show', $level->id) }}">View</a> -->
                                                <a class="btn btn-warning" href="{{ route('level_views.edit', $level->id) }}">Edit</a>
                                                <form action="{{ route('level_views.destroy', $level->id) }}"
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
        function initFilters() {
            const url = new URL(window.location.href);
            document.getElementById("id-filter").value = url.searchParams.get('id');
            document.getElementById("order-filter").value = url.searchParams.get('order_no');
            document.getElementById("title-filter").value = url.searchParams.get('title');
        }
        window.onload = initFilters;
    </script>
@endsection
