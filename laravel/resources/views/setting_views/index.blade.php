@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">Settings</div>
            <div class="panel-body">
                @foreach($settings as $setting)
                    <form action="{{ route('settings.update') }}" method="POST">
                        <div class="col-md-4 form-group">
                            <label for="email" class="col-md-4 control-label">{{ $setting->label }}</label>
                            <div class="col-md-8">
                                <input type="text" name="value" class="form-control" value="{{ $setting->value }}">
                            </div>
                        </div>
                        <div class="col-md-4 form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button class="btn btn-primary" type="submit" >Save</button>
                            </div>
                        </div>
                        <input type="hidden" name="id" value="{{ $setting->id }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="PATCH">
                    </form>
                @endforeach
            </div>
        </div>
    </div>

@endsection
