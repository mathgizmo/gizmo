@extends('layouts.auth')

@section('title', 'Gizmo - Admin: Reset Password')

@section('content')
    <div class="card shadow-lg border-0 rounded-lg mt-5">
        <div class="card-header">
            <h3 class="text-center font-weight-light my-4">
                Password Recovery
            </h3>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <form role="form" method="POST" action="{{ url('/password/email') }}">
                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="small mb-1" for="inputEmailAddress">
                        Email
                    </label>
                    <input class="form-control py-4" id="inputEmailAddress" type="email" name="email" value="{{ old('email') }}"
                           aria-describedby="emailHelp" placeholder="Enter email address"/>
                    @if ($errors->has('email'))
                        <span class="form-text">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                    <a class="small" href="../login">
                        Return to login
                    </a>
                    <button type="submit" class="btn btn-dark">
                        <i class="fa fa-btn fa-envelope"></i> Send Password Reset Link
                    </button>
                </div>
                {{ csrf_field() }}
            </form>
        </div>
        <div class="card-footer text-center">
            <div class="small"><a href="../register">Need an account? Sign up!</a></div>
        </div>
    </div>
@endsection
