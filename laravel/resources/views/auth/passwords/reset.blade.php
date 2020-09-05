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
            <form role="form" method="POST" action="{{ url('/password/reset') }}">
                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="small mb-1" for="inputEmailAddress">
                        Email
                    </label>
                    <input class="form-control py-4" id="inputEmailAddress" type="email" name="email" value="{{ $email or old('email') }}"
                           aria-describedby="emailHelp" placeholder="Enter email address"/>
                    @if ($errors->has('email'))
                        <span class="form-text">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                            <label class="small mb-1" for="inputPassword">
                                Password
                            </label>
                            <input class="form-control py-4" id="inputPassword" type="password" name="password"
                                   placeholder="Enter password"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label class="small mb-1" for="inputConfirmPassword">
                                Confirm Password
                            </label>
                            <input class="form-control py-4" id="inputConfirmPassword" type="password" name="password_confirmation" placeholder="Confirm password"/>
                        </div>
                    </div>
                    @if ($errors->has('password'))
                        <span class="form-text" style="margin-top: -16px; margin-left: 6px;">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                    <a class="small" href="../../login">
                        Return to login
                    </a>
                    <button type="submit" class="btn btn-dark">
                        <i class="fas fa-redo"></i> Reset Password
                    </button>
                </div>
                {{ csrf_field() }}
                <input type="hidden" name="token" value="{{ $token }}">
            </form>
        </div>
        <div class="card-footer text-center">
            <div class="small"><a href="../../register">Need an account? Sign up!</a></div>
        </div>
    </div>
@endsection
