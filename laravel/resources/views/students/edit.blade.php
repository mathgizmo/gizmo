@extends('layouts.app')

@section('title', 'Gizmo - Admin: Students')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('students.index')  }}">Manage Students</a></li>
    <li class="breadcrumb-item active">Edit Student</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Edit Student</div>
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
                    <label for="name">Username</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static"> {{ $student->name }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label for="first_name">First Name</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static"> {{ $student->first_name ?: 'none' }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label for="last_name">Last Name</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static"> {{ $student->last_name ?: 'none' }}</p>
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
            <form class="row mb-3" action="{{ route('students.super', $student->id) }}" method="POST">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label for="is_super">Super Access</label>
                </div>
                <div class="col-md-8">
                    <select id="is_super" name="is_super" class="form-control" onchange="this.parentElement.parentElement.submit();" style="max-width: 300px;">
                        <option {{ $student->is_super ? 'selected="selected"' : '' }} value="1">Yes</option>
                        <option {{ $student->is_super ? '' : 'selected="selected"' }} value="0">No</option>
                    </select>
                </div>
                <input type="hidden" name="_method" value="PATCH">
                {{ csrf_field() }}
            </form>
            <form class="row mb-3" action="{{ route('students.teacher', $student->id) }}" method="POST">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label for="is_teacher">Teacher</label>
                </div>
                <div class="col-md-8">
                    <select id="is_teacher" name="is_teacher" class="form-control" onchange="this.parentElement.parentElement.submit();" style="max-width: 300px;">
                        <option {{ $student->is_teacher ? 'selected="selected"' : '' }} value="1">Yes</option>
                        <option {{ $student->is_teacher ? '' : 'selected="selected"' }} value="0">No</option>
                    </select>
                </div>
                <input type="hidden" name="_method" value="PATCH">
                {{ csrf_field() }}
            </form>
            <form class="d-flex flex-row mx-3 mb-3" action="{{ route('students.reset', $student->id) }}"
                  method="POST" style="display: inline;"
                  onsubmit="if(confirm('This will remove all participant progress? Are you sure?')) { return true } else {return false };">
                <input type="hidden" name="_method" value="POST">
                {{ csrf_field() }}
                <button class="btn btn-danger btn-sm" type="submit">Reset Progress</button>
            </form>
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
