@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Users')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('users.index')  }}">Manage Users</a></li>
    <li class="breadcrumb-item active">Edit User</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Edit User</div>
        <form role="form" action="{{ route('users.update', $user->id) }}" method="POST">
        <div class="card-body p-0">
                {{ csrf_field() }}
                <div class="form-group row mt-3 {{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="question" class="col-md-2 form-control-label ml-3 font-weight-bold">Email</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="email" value="{{ old('email') ? old('email') : $user->email }}">
                        @if ($errors->has('email'))
                            <span class="form-text">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="question" class="col-md-2 form-control-label ml-3 font-weight-bold">Name</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="name" value="{{ old('name') ? old('name') : $user->name }}">
                        @if ($errors->has('name'))
                            <span class="form-text">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="question" class="col-md-2 form-control-label ml-3 font-weight-bold">Password</label>
                    <div class="col-md-8">
                        <input type="password" class="form-control" name="password" value="{{ old('password') }}">
                        @if ($errors->has('password'))
                            <span class="form-text">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="role-select" class="col-md-2 form-control-label ml-3 font-weight-bold">Role</label>
                    <div class="col-md-8">
                        <select class="form-control" id="role-select" name="role">
                            <option value="questions_editor" {{ $user->isQuestionsEditor() ? 'selected' : '' }}>Questions Editor</option>
                            <option value="admin" {{ $user->isAdmin() ? 'selected' : '' }}>Admin</option>
                            <option value="superadmin" {{ $user->isSuperAdmin() ? 'selected' : '' }}>Super Admin</option>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="_method" value="patch">
        </div>
            <div class="card-footer">
                <a class="btn btn-secondary"
                   href="{{ route('users.index') }}">Back</a>
                <button class="btn btn-dark" type="submit">Submit</button>
            </div>
        </form>
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
