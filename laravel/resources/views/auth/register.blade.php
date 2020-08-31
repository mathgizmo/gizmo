@extends('layouts.auth')

@section('title', 'Gizmo - Admin: Register')

@section('content')
    <div class="card shadow-lg border-0 rounded-lg mt-5">
        <div class="card-header"><h3 class="text-center font-weight-light my-4">Create Account</h3></div>
        <div class="card-body">
            <form role="form" method="POST" action="{{ url('/register') }}" id="registerForm">
                <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                    <label class="small mb-1" for="inputName">
                        Name
                    </label>
                    <input class="form-control py-4" id="inputName" type="text" name="name" value="{{ old('name') }}" aria-describedby="nameHelp" placeholder="Enter name"/>
                    @if ($errors->has('name'))
                        <span class="form-text">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="small mb-1" for="inputEmailAddress">
                        Email
                    </label>
                    <input class="form-control py-4" id="inputEmailAddress" type="email" name="email" value="{{ old('email') }}" aria-describedby="emailHelp"  placeholder="Enter email address"/>
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
                <div class="form-group mt-4 mb-0">
                    <button type="submit" class="btn btn-dark btn-block g-recaptcha"
                            data-sitekey="{{Illuminate\Support\Facades\Config::get('auth.recaptcha.key')}}"
                            data-callback='onSubmit'>
                        <i class="fa fa-btn fa-user"></i> Create Account
                    </button>
                </div>
                {{ csrf_field() }}
            </form>
        </div>
        <div class="card-footer text-center">
            <div class="small">
                <a href="./login">
                    Have an account? Go to login
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function onSubmit(token) {
            document.getElementById("registerForm").submit();
        }
    </script>
@endsection
