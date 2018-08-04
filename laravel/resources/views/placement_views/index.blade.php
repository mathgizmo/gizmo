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
                                            <th class="col-md">ID</th>
                                            <th class="col-md">Order No</th>
                                            <th class="col-md">Question</th>
                                            <th class="col-md">Unit</th>
                                            <th class="col-md">Active</th>
                                            <th class="col-md-3">OPTIONS</th>
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
