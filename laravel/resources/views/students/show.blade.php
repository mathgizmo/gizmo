@extends('layouts.app')

@section('title', 'Gizmo - Admin: Students')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('students.index')  }}">Manage Students</a></li>
    <li class="breadcrumb-item active">Student Details</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Student Details</div>
        <div class="card-body p-0">
            <div class="row mt-3">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label for="name">E-mail</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static"> {{ $student->email }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label for="name">Name</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static"> {{ $student->name }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label for="name">Registration time</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static"> {{ $student->created_at? $student->created_at->format('Y/m/d H:i') : '' }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label for="name">Last action time</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static"> {{ $student->students_tracking->last() != null ? date('H:i d.m.Y', strtotime($student->students_tracking->last()->date)) : 'Never' }}</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Lesson ID</th>
                        <th>Date</th>
                        <th>Action</th>
                        <th>Start_datetime</th>
                        <th>Weak questions</th>
                        <th>Ip address</th>
                        <th>User-Agent</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($student->students_tracking->sortByDesc('id') as $students_tracking)
                        <tr>
                            <td>{{ $students_tracking->lesson_id }}</td>
                            <td>{{ date('H:i d.m.Y', strtotime($students_tracking->date)) }}</td>
                            <td>{{ $students_tracking->action }}</td>
                            <td>{{ $students_tracking->start_datetime }}</td>
                            <td>{{ $students_tracking->weak_questions }}</td>
                            <td>{{ $students_tracking->ip }}</td>
                            <td>{{ $students_tracking->user_agent }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
