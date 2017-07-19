@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Question / Show </div>

                <div class="panel-body">

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="col-md-1">ID</th>
                        <th class="col-md-2">TITLE</th>
                        <th class="col-md-2">START</th>
                        <th class="col-md-2">END</th>
                        <th class="col-md-1">IS_ALL_DAY</th>
                        <th class="col-md-1">BACKGROUND_COLOR</th>
                        <th class=" col-md-3 text-right">OPTIONS</th>
                    </tr>
                </thead>

                <tbody>

      

                </tbody>
            </table>

            <a class="btn btn-success" href="{{ Route('question_views.create') }}">Create</a>
        </div>
    </div>
             </div>
            </div>
        </div>
    </div>
</div>

@endsection