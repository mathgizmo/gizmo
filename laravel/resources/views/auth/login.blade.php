@extends('layouts.auth')

@section('title', 'Gizmo - Admin: Login')

@section('content')
    <div class="card shadow-lg border-0 rounded-lg mt-5">
        <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>
        <div class="card-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}" id="loginForm">
                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="small mb-1" for="inputEmailAddress">
                        Email
                    </label>
                    <input class="form-control py-4" id="inputEmailAddress" type="email" name="email" value="{{ old('email') }}" placeholder="Enter email address"/>
                    @if ($errors->has('email'))
                        <span class="form-text">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                    <label class="small mb-1" for="inputPassword">
                        Password
                    </label>
                    <input class="form-control py-4" id="inputPassword" type="password" name="password" placeholder="Enter password"/>
                    @if ($errors->has('password'))
                        <span class="form-text">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" id="rememberPasswordCheck" type="checkbox" name="remember"/>
                        <label class="custom-control-label" for="rememberPasswordCheck">Remember Me</label>
                    </div>
                </div>
                <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                    <a class="small" href="./password/reset">
                        Forgot Password?</a>
                    <button type="submit" class="btn btn-dark g-recaptcha"
                            data-sitekey="{{Illuminate\Support\Facades\Config::get('auth.recaptcha.key')}}"
                            data-callback='onSubmit'>
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </button>
                </div>
                {{ csrf_field() }}
            </form>
        </div>
        <div class="card-footer text-center">
            <div class="small"><a href="./register">Need an account? Sign up!</a></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function onSubmit(token) {
            document.getElementById("loginForm").submit();
        }
    </script>
@endsection
