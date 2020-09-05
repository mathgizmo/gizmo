@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Users')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manage Users</li>
@endsection

@section('content')
    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row justify-content-between">
            Users
            <div class="pull-right">
                <a href="{{ route('users.create') }}" class="btn btn-info btn-sm" style="margin-top: -5px;">+ add user</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>
                                    ID
                                    <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                        <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                                    </a>
                                </th>
                                <th>
                                    Name
                                    <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'name', 'order' => ((request()->sort == 'name' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'name' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                        <i class="fa fa-fw fa-sort{{ (request()->sort == 'name' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'name' && request()->order == 'desc') ? '-down' : '' }}"></i>
                                    </a>
                                </th>
                                <th>
                                    Email
                                    <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'email', 'order' => ((request()->sort == 'email' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'email' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                        <i class="fa fa-fw fa-sort{{ (request()->sort == 'email' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'email' && request()->order == 'desc') ? '-down' : '' }}"></i>
                                    </a>
                                </th>
                                <th>
                                    Role
                                </th>
                                <th style="min-width: 180px;"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->roleName() }}</td>
                                    <td class="d-flex flex-wrap flex-row justify-content-end">
                                        <a href="users/{{ $user->id }}/edit" class="btn btn-info" style="margin-right: 4px;">Edit</a>
                                        <form method="POST" action="users/{{ $user->id }}">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="_method" value="delete">
                                            <button type="button" class="btn btn-danger">Delete</button>
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
               $('.btn-danger').on('click', function(event) {
                  event.preventDefault();
                  var _delete = confirm("Do you really want to delete this user?");

                  if (_delete) {
                      $(this).closest('form').trigger('submit');
               }
            });
        });
    </script>
@endsection
