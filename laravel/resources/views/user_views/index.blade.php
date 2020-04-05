@extends('layouts.app')

@section('content')

    <div class="container">
        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <div class="panel panel-default">
            <div class="panel-heading">
                Users
                <div class="pull-right">
                    <a href="{{ route('users.create') }}" class="btn btn-info btn-sm" style="margin-top: -5px;">+ add user</a>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="col-md">
                                        ID
                                        <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                        </a>
                                    </th>
                                    <th class="col-md">
                                        Name
                                        <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'name', 'order' => ((request()->sort == 'name' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'name' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'name' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'name' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                        </a>
                                    </th>
                                    <th class="col-md">
                                        Email
                                        <a href="{{ route('users.index', array_merge(request()->all(), ['sort' => 'email', 'order' => ((request()->sort == 'email' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'email' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'email' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'email' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                        </a>
                                    </th>
                                    <th class="col-md">
                                        Role
                                    </th>
                                    <th class="col-md"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->roleName() }}</td>
                                        <td>
                                            <div style="display: flex; flex-direction: row;">
                                                <a href="users/{{ $user->id }}/edit" class="btn btn-info" style="margin-right: 4px;">Edit</a>
                                                <form method="POST" action="users/{{ $user->id }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="_method" value="delete">
                                                    <button type="button" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
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
