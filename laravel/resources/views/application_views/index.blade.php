@extends('layouts.app')

@section('content')

<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <div class="panel-title pull-left">
                Applications List
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('application_views.create') }}">Create</a>

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
                                                Icon
                                            </th>
                                            <th class="col-md">
                                                ID
                                                <a href="{{ route('application_views.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                                    <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                                </a>
                                            </th>
                                            <th class="col-md">
                                                Name
                                                <a href="{{ route('application_views.index', array_merge(request()->all(), ['sort' => 'name', 'order' => ((request()->sort == 'name' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'name' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                                    <i class="fa fa-fw fa-sort{{ (request()->sort == 'name' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'name' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                                </a>
                                            </th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <tr style="background: #999999;">
                                            <td></td>
                                            <td>
                                                <input type="number" min="0" name="id" id="id-filter" style="width: 50px;">
                                            </td>
                                            <td>
                                                <input type="text" name="name" id="name-filter" style="width: 100%;">
                                            </td>
                                            <td class="text-right">
                                                <a href="javascript:void(0);" onclick="filter()" class="btn btn-primary">Filter</a>
                                            </td>
                                        </tr>

                                        @foreach($applications as $application)
                                        <tr>
                                            <td style="padding: 0 !important;">
                                                <img src="{{ URL::asset($application->icon()) }}" alt="" style="max-width: 60px;">
                                            </td>
                                            <td>{{$application->id}}</td>
                                            <td>{{$application->name}}</td>
                                            <td class="text-right">
                                                <a class="btn btn-warning" href="{{ route('application_views.edit', $application->id) }}">Edit</a>
                                                <form action="{{ route('application_views.destroy', $application->id) }}"
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
            const name = document.getElementById("name-filter").value;
            if(id) {
                url.searchParams.set('id', id);
            } else if (url.searchParams.get('id')) {
                url.searchParams.delete('id');
            }
            if(name) {
                url.searchParams.set('name', name);
            } else if (url.searchParams.get('name')) {
                url.searchParams.delete('name');
            }
            window.location.href = url.toString();
        } 
        function initFilters() {
            const url = new URL(window.location.href);
            document.getElementById("id-filter").value = url.searchParams.get('id');
            document.getElementById("name-filter").value = url.searchParams.get('name');
        }
        window.onload = initFilters;
    </script>
@endsection
