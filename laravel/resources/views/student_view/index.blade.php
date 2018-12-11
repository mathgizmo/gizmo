@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">Student Detail</div>
            <div class="panel-body">
                <form action="{{ route('students.index', request()->all()) }}">
                    <div class="col-md-4 form-group">
                        <label for="email" class="col-md-4 control-label">E-mail</label>
                        <div class="col-md-8">
                            <input type="text" name="email" class="form-control" value="{{ request()->email }}">
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="name" class="col-md-4 control-label">Name</label>
                        <div class="col-md-8">
                            <input type="text" name="name" class="form-control" value="{{ request()->name }}">
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button class="btn btn-primary" type="submit" >Search</button>
                        </div>
                    </div>
                    <input type="hidden" name="sort" value="{{ request()->sort }}">
                    <input type="hidden" name="order" value="{{ request()->order }}">
                </form>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">Students</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="col-md"></th>
                                    <th class="col-md">
                                        ID <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                        </a>
                                    </th>
                                    <th class="col-md">
                                        Name <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'name', 'order' => ((request()->sort == 'name' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'name' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'name' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'name' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                        </a>
                                    </th>
                                    <th class="col-md">
                                        Email <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'email', 'order' => ((request()->sort == 'email' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'email' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'email' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'email' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                        </a>
                                    </th>
                                    <th class="col-md">
                                        Registration time <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'created_at', 'order' => ((request()->sort == 'created_at' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'created_at' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'created_at' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'created_at' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                        </a>
                                    </th>
                                    <th class="col-md">
                                        Last action time <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'date', 'order' => ((request()->sort == 'date' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'date' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'date' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'date' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                        </a>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($students as $student)
                                    <tr>
                                            <td>
                                            <div class="btn-group">
                                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-info">Show</a>
                                            </div>
                                            <form action="{{ route('students.super', $student->id) }}" method="POST" style="display: inline;">
                                                <input type="hidden" name="_method" value="PATCH">
                                                {{ csrf_field() }}
                                                <button class="btn btn-warning" type="submit">{{ $student->is_super ? 'Revoke super access' : 'Add super access' }}</button>
                                            </form>
                                            <form action="{{ route('students.reset', $student->id) }}"
                                                  method="POST" style="display: inline;"
                                                  onsubmit="if(confirm('This will remove all participant progress? Are you sure?')) { return true } else {return false };">
                                                <input type="hidden" name="_method" value="POST">
                                                {{ csrf_field() }}
                                                <button class="btn btn-danger" type="submit">Reset Progress</button>
                                            </form>
                                            <form action="{{ route('students.delete', $student->id) }}"
                                                  method="POST" style="display: inline;"
                                                  onsubmit="if(confirm('Are you about to delete {{ $student->name }}, all participant information will be lost. This action is irreversible.')) { return true } else {return false };">
                                                <input type="hidden" name="_method" value="POST">
                                                {{ csrf_field() }}
                                                <button class="btn btn-danger" type="submit">Delete</button>
                                            </form>
                                        </td>
                                        <td>{{ $student->id }}</td>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->created_at? $student->created_at->format('Y/m/d H:i') : '' }}</td>
                                        <td>{{ $student->date != null ? date('H:i d.m.Y', strtotime($student->date)) : 'Never' }}</td>
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

@endsection
