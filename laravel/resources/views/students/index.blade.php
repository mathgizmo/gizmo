@extends('layouts.app')

@section('title', 'Gizmo - Admin: Students')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manage Students</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Students</div>
        <div class="card-body p-0">
            <form class="filters-container d-flex flex-row flex-wrap m-2" action="{{ route('students.index', request()->all()) }}">
                <div class="filter input-group mr-2">
                    <div class="input-group-prepend">
                        <label for="email" class="input-group-text">E-mail</label>
                    </div>
                    <input type="text" name="email" class="form-control" value="{{ request()->email }}"/>
                </div>
                <div class="filter input-group mr-2">
                    <div class="input-group-prepend">
                        <label for="name" class="input-group-text">Name</label>
                    </div>
                    <input type="text" name="name" class="form-control" value="{{ request()->name }}"/>
                </div>
                <input type="hidden" name="sort" value="{{ request()->sort }}">
                <input type="hidden" name="order" value="{{ request()->order }}">
                <button type="submit" class="filter-button btn btn-outline-secondary" style="max-width: 195px;">
                    Search
                </button>
            </form>
            <div class="d-flex justify-content-center mt-2">
                {{ $students->links() }}
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th style="min-width: 80px;">
                            ID <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 120px;">
                            Name <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'name', 'order' => ((request()->sort == 'name' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'name' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'name' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'name' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 120px;">
                            Email <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'email', 'order' => ((request()->sort == 'email' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'email' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'email' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'email' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 120px;">
                            Registration time <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'created_at', 'order' => ((request()->sort == 'created_at' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'created_at' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'created_at' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'created_at' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 120px;">
                            Last action time <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'date', 'order' => ((request()->sort == 'date' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'date' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'date' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'date' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 280px;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td>{{ $student->id }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->created_at? $student->created_at->format('Y/m/d H:i') : '' }}</td>
                            <td>{{ $student->date != null ? date('H:i d.m.Y', strtotime($student->date)) : 'Never' }}</td>
                            <td class="d-flex flex-column flex-wrap">
                                <div class="flex flex-row justify-content-end mb-2">
                                    <div class="btn-group">
                                        <a href="{{ route('students.show', $student->id) }}" class="btn btn-info">Show</a>
                                    </div>
                                    <form action="{{ route('students.super', $student->id) }}" method="POST" style="display: inline;">
                                        <input type="hidden" name="_method" value="PATCH">
                                        {{ csrf_field() }}
                                        <button class="btn btn-dark" type="submit">{{ $student->is_super ? 'Revoke super access' : 'Add super access' }}</button>
                                    </form>
                                </div>
                                <div class="flex flex-row justify-content-end">
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
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-2">
                {{ $students->links() }}
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .input-group-text {
            min-width: 120px;
            white-space: initial;
            text-align: left;
        }

        .filter {
            width: 380px;
        }

        @media (max-width: 1280px) {
            .filters-container > div {
                flex-direction: column !important;
                margin: 0 !important;
            }

            .filters-container .filter > *:not(a) {
                max-width: 100% !important;
                min-width: 100% !important;
                width: 100% !important;
                margin-bottom: 8px;
            }
        }
    </style>
@endsection
