@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Classes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('classes.index')  }}">Manage Classes</a></li>
    <li class="breadcrumb-item active">Manage Students</li>
@endsection

@section('content')
    @if(Session::has('flash_message'))
        <div id="successMessage" class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>
            <em> {!! session('flash_message') !!}</em>
        </div>
    @endif
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Students of {{$class->name}}</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Email
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td>{{$student->id}}</td>
                            <td>{{$student->first_name . ' ' . $student->last_name}}</td>
                            <td>{{$student->email}}</td>
                            <td class="text-right" style="min-width: 260px;">
                                <a class="btn btn-outline-dark" href="{{route('students.edit', $student->id)}}">Show</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script>
        $(document).ready(function () {
            setTimeout(function () {
                $('#successMessage').fadeOut('fast');
            }, 4000); // <-- time in milliseconds
        });
    </script>

@endsection

@section('styles')
    <style>
        td {
            max-width: 240px !important;
        }
    </style>
@endsection
