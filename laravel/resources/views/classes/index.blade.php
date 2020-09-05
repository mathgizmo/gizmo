@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Classes')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manage Classes</li>
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
            Manage Classes
            <a class="btn btn-dark btn-sm" href="{{ route('classes.create') }}">+ add class</a>
        </div>
        <div class="card-body p-0">
            <div class="d-flex justify-content-center mt-2">
                {{ $classes->links() }}
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th style="min-width: 60px;">
                            ID
                            <a href="{{ route('classes.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 180px;">
                            Name
                            <a href="{{ route('classes.index', array_merge(request()->all(), ['sort' => 'name', 'order' => ((request()->sort == 'name' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'name' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'name' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'name' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 180px;">
                            Teacher
                            <a href="{{ route('classes.index', array_merge(request()->all(), ['sort' => 'teacher', 'order' => ((request()->sort == 'teacher' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'teacher' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'teacher' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'teacher' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 180px;">
                            Type of classroom
                        </th>
                        <th style="min-width: 180px;">
                            Subscription Type
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
                            <input type="text" name="name" id="name-filter" style="width: 100%;">
                        </td>
                        <td>
                            <input type="text" name="teacher" id="teacher-filter"  list="teachers-datalist" style="width: 100%;">
                            <datalist id="teachers-datalist"></datalist>
                        </td>
                        <td>
                            <select class="form-control" name="class_type" id="class-type-filter">
                                <option></option>
                                <option value="elementary">Elementary</option>
                                <option value="secondary">Secondary</option>
                                <option value="college">College</option>
                                <option value="university">University</option>
                                <option value="professional">Professional</option>
                                <option value="other">Other</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="subscription_type" id="subscription-type-filter">
                                <option></option>
                                <option value="open">Open</option>
                                <option value="invitation">Invitation</option>
                                <option value="closed">Closed</option>
                            </select>
                        </td>
                        <td class="text-right">
                            <a href="javascript:void(0);" onclick="filter()" class="btn btn-dark">Filter</a>
                        </td>
                    </tr>

                    @foreach($classes as $class)
                        <tr>
                            <td>{{$class->id}}</td>
                            <td>{{$class->name}}</td>
                            <td>{{$class->teacher ? $class->teacher->name : ''}}</td>
                            <td>{{ucfirst($class->class_type)}}</td>
                            <td>{{$class->subscription_type == 'closed' ? 'Closed' : ($class->subscription_type == 'invitation' ? 'Invitation Only' : 'Open')}}</td>
                            <td class="text-right" style="min-width: 260px;">
                                <a class="btn btn-outline-dark" href="{{ route('classes.students.index', $class->id) }}">Manage Students</a>
                                <a class="btn btn-dark" href="{{ route('classes.edit', $class->id) }}">Edit</a>
                                <form action="{{ route('classes.destroy', $class->id) }}"
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
            <div class="d-flex justify-content-center mt-2">
                {{ $classes->links() }}
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
            const subscription_type = document.getElementById("subscription-type-filter").value;
            const class_type = document.getElementById("class-type-filter").value;
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
            if(subscription_type) {
                url.searchParams.set('subscription_type', subscription_type);
            } else if (url.searchParams.get('subscription_type')) {
                url.searchParams.delete('subscription_type');
            }
            if(class_type) {
                url.searchParams.set('class_type', class_type);
            } else if (url.searchParams.get('class_type')) {
                url.searchParams.delete('class_type');
            }
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        function initFilters() {
            const url = new URL(window.location.href);
            document.getElementById("id-filter").value = url.searchParams.get('id');
            document.getElementById("name-filter").value = url.searchParams.get('name');
            document.getElementById("teacher-filter").value = url.searchParams.get('teacher');
            document.getElementById("subscription-type-filter").value = url.searchParams.get('subscription_type');
            document.getElementById("class-type-filter").value = url.searchParams.get('class_type');

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
                            option.value = item.name;
                            dl.appendChild(option);
                        });
                    }
                });
            }
        });
    </script>
@endsection
