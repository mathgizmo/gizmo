@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Applications')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manage Applications</li>
@endsection

@section('content')
    @if(Session::has('message'))
        <div id="successMessage" class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>
            <em> {!! session('message') !!}</em>
        </div>
    @endif
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row justify-content-between">
            Manage Applications
            <div class="d-flex">
                <a class="btn btn-dark btn-sm" href="{{ route('applications.create', ['type' => 'assignment']) }}">+ add assignment</a>
                <a class="btn btn-dark btn-sm ml-1" href="{{ route('applications.create', ['type' => 'test']) }}">+ add test</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="d-flex justify-content-center mt-2" style="max-width: 100%;">
                {{ $applications->links() }}
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th style="min-width: 60px;">
                            Icon
                        </th>
                        <th style="min-width: 60px;">
                            ID
                            <a href="{{ route('applications.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 120px;">
                            Name
                            <a href="{{ route('applications.index', array_merge(request()->all(), ['sort' => 'name', 'order' => ((request()->sort == 'name' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'name' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'name' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'name' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 120px;">
                            Teacher
                            <a href="{{ route('applications.index', array_merge(request()->all(), ['sort' => 'teacher', 'order' => ((request()->sort == 'teacher' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'teacher' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'teacher' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'teacher' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 180px;">
                            Type
                        </th>
                        <th style="min-width: 180px;"></th>
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
                        <td>
                            <input type="text" name="teacher" id="teacher-filter" list="teachers-datalist" style="width: 100%;">
                            <datalist id="teachers-datalist"></datalist>
                        </td>
                        <td>
                            <select class="form-control" name="type" id="type-filter">
                                <option></option>
                                <option value="assignment">Assignment</option>
                                <option value="test">Test</option>
                            </select>
                        </td>
                        <td class="text-right">
                            <a href="javascript:void(0);" onclick="filter()" class="btn btn-dark">Filter</a>
                        </td>
                    </tr>

                    @foreach($applications as $application)
                        <tr>
                            <td style="padding: 0 !important;">
                                <img src="{{ URL::asset($application->icon()) }}" alt="" style="max-width: 60px;">
                            </td>
                            <td>{{$application->id}}</td>
                            <td>{{$application->name}}</td>
                            <td>{{$application->teacher ? $application->teacher->email : ''}}</td>
                            <td>{{ucfirst($application->type)}}</td>
                            <td class="text-right">
                                <form action="{{ route('applications.copy', $application->id) }}"
                                      method="POST" style="display: inline;">
                                    <input type="hidden" name="_method" value="POST">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button class="btn btn-outline-dark" type="submit">Copy</button>
                                </form>
                                <a class="btn btn-dark" href="{{ route('applications.edit', $application->id) }}">Edit</a>
                                <form action="{{ route('applications.destroy', $application->id) }}"
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
            <div class="d-flex justify-content-center mt-2" style="max-width: 100%;">
                {{ $applications->links() }}
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
            const teacher = document.getElementById("teacher-filter").value;
            const type = document.getElementById("type-filter").value;
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
            if(teacher) {
                url.searchParams.set('teacher', teacher);
            } else if (url.searchParams.get('teacher')) {
                url.searchParams.delete('teacher');
            }
            if(type) {
                url.searchParams.set('type', type);
            } else if (url.searchParams.get('type')) {
                url.searchParams.delete('type');
            }
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        function initFilters() {
            const url = new URL(window.location.href);
            document.getElementById("id-filter").value = url.searchParams.get('id');
            document.getElementById("name-filter").value = url.searchParams.get('name');
            document.getElementById("teacher-filter").value = url.searchParams.get('teacher');
            document.getElementById("type-filter").value = url.searchParams.get('type');
        }
        window.onload = initFilters;

        $('#teacher-filter').keyup(function() {
            const pattern = $('#teacher-filter').val();
            if(pattern) {
                $.ajax({
                    url: "{{route('students.search')}}?pattern="+pattern+'&is_teacher=1&limit=5',
                    type: "GET",
                    success: function(data, textStatus, jqXHR) {
                        const dl = document.getElementById('teachers-datalist');
                        dl.innerHTML = '';
                        data.forEach((item, index) => {
                            const option = document.createElement('option');
                            option.value = item.email;
                            option.label = item.last_name ? (item.first_name + ' ' + item.last_name) : item.email;
                            dl.appendChild(option);
                        });
                    }
                });
            }
        });
    </script>
@endsection
