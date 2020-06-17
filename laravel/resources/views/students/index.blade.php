@extends('layouts.app')

@section('title', 'Gizmo - Admin: Students')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manage Students</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Manage Students</div>
        <div class="card-body p-0">
            <div class="d-flex justify-content-center mt-2">
                {{ $students->links() }}
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>
                            ID <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Username <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'name', 'order' => ((request()->sort == 'name' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'name' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'name' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'name' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            First Name <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'first_name', 'order' => ((request()->sort == 'first_name' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'first_name' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'first_name' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'first_name' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Last Name <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'last_name', 'order' => ((request()->sort == 'last_name' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'last_name' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'last_name' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'last_name' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Email <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'email', 'order' => ((request()->sort == 'email' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'email' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'email' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'email' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Registration time <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'created_at', 'order' => ((request()->sort == 'created_at' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'created_at' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'created_at' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'created_at' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Last action time <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'date', 'order' => ((request()->sort == 'date' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'date' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'date' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'date' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Superuser
                        </th>
                        <th>
                            Teacher
                        </th>
                        <th style="min-width: 160px;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr style="background: #999999;">
                        <td>
                            <input type="number" min="0" name="id" id="id-filter" style="width: 50px;">
                        </td>
                        <td>
                            <input type="text" name="name" id="name-filter" style="width: 100px;">
                        </td>
                        <td>
                            <input type="text" name="first_name" id="first-name-filter" style="width: 110px;">
                        </td>
                        <td>
                            <input type="text" name="last_name" id="last-name-filter" style="width: 110px;">
                        </td>
                        <td>
                            <input type="email" name="email" id="email-filter" style="width: 140px;">
                        </td>
                        <td></td>
                        <td></td>
                        <td>
                            <select id="is-super-filter" name="is_super" class="form-control" style="min-width: 80px;">
                                <option value="">Any</option>
                                <option value="yes">Superuser</option>
                                <option value="no">Not Superuser</option>
                            </select>
                        </td>
                        <td>
                            <select id="is-teacher-filter" name="is_teacher" class="form-control" style="min-width: 80px;">
                                <option value="">Any</option>
                                <option value="yes">Teacher</option>
                                <option value="no">Not Teacher</option>
                            </select>
                        </td>
                        <td class="text-right">
                            <a href="javascript:void(0);" onclick="filter()" class="btn btn-dark">Filter</a>
                        </td>
                    </tr>
                    @foreach($students as $student)
                        <tr>
                            <td>{{ $student->id }}</td>
                            <td style="max-width: 120px;">{{ $student->name }}</td>
                            <td style="max-width: 120px;">{{ $student->first_name }}</td>
                            <td style="max-width: 120px;">{{ $student->last_name }}</td>
                            <td style="max-width: 160px;">{{ $student->email }}</td>
                            <td style="max-width: 80px;">{{ $student->created_at? $student->created_at->format('Y/m/d H:i') : '' }}</td>
                            <td style="max-width: 80px;">{{ $student->date != null ? date('H:i d.m.Y', strtotime($student->date)) : 'Never' }}</td>
                            <td style="max-width: 40px;">{{ $student->is_super ? 'Yes' : 'No' }}</td>
                            <td style="max-width: 40px;">{{ $student->is_teacher ? 'Yes' : 'No' }}</td>
                            <td class="flex flex-row justify-content-end mb-2">
                                <div class="btn-group">
                                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-dark">Edit</a>
                                </div>
                                <form action="{{ route('students.delete', $student->id) }}"
                                      method="POST" style="display: inline;"
                                      onsubmit="if(confirm('Are you about to delete {{ $student->name }}, all participant information will be lost. This action is irreversible.')) { return true } else {return false };">
                                    <input type="hidden" name="_method" value="POST">
                                    {{ csrf_field() }}
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
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

@section('scripts')
    <script type="text/javascript">
        function filter() {
            let url = new URL(window.location.href);
            const id = document.getElementById("id-filter").value;
            const name = document.getElementById("name-filter").value;
            const first_name = document.getElementById("first-name-filter").value;
            const last_name = document.getElementById("last-name-filter").value;
            const email = document.getElementById("email-filter").value;
            const is_super = document.getElementById("is-super-filter").value;
            const is_teacher = document.getElementById("is-teacher-filter").value;

            if (id) {
                url.searchParams.set('id', id);
            } else if (url.searchParams.get('id')) {
                url.searchParams.delete('id');
            }
            if (name) {
                url.searchParams.set('name', name);
            } else if (url.searchParams.get('name')) {
                url.searchParams.delete('name');
            }
            if (first_name) {
                url.searchParams.set('first_name', first_name);
            } else if (url.searchParams.get('first_name')) {
                url.searchParams.delete('first_name');
            }
            if (last_name) {
                url.searchParams.set('last_name', last_name);
            } else if (url.searchParams.get('last_name')) {
                url.searchParams.delete('last_name');
            }
            if (email) {
                url.searchParams.set('email', email);
            } else if (url.searchParams.get('email')) {
                url.searchParams.delete('email');
            }
            if (is_super) {
                url.searchParams.set('is_super', is_super);
            } else if (url.searchParams.get('is_super')) {
                url.searchParams.delete('is_super');
            }
            if (is_teacher) {
                url.searchParams.set('is_teacher', is_teacher);
            } else if (url.searchParams.get('is_teacher')) {
                url.searchParams.delete('is_teacher');
            }
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }

        function init() {
            const url = new URL(window.location.href);
            document.getElementById("id-filter").value = url.searchParams.get('id');
            document.getElementById("name-filter").value = url.searchParams.get('name');
            document.getElementById("first-name-filter").value = url.searchParams.get('first_name');
            document.getElementById("last-name-filter").value = url.searchParams.get('last_name');
            document.getElementById("email-filter").value = url.searchParams.get('email');
            document.getElementById("is-super-filter").value = url.searchParams.get('is_super');
            document.getElementById("is-teacher-filter").value = url.searchParams.get('is_teacher');
        }

        window.onload = init;
    </script>
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
