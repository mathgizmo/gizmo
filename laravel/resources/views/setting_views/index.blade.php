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

        <div class="panel panel-default">
            <div class="panel-heading">Welcome Page Texts</div>
            <div class="panel-body">
                @foreach($welcome_texts as $welcome_text)
                    <form action="{{ route('settings.update') }}" method="POST">
                        <div class="col-md-4 form-group">
                            <label class="control-label text-center" style="width: 100%;">{{ $welcome_text->label }}</label>
                            <!-- <input type="text" name="label" class="form-control" value="{{ $welcome_text->label }}"> -->
                            <textarea name="value" class="form-control" style="min-height: 150px; margin-top: 8px;">{{ $welcome_text->value }}</textarea>
                            <button class="btn btn-primary" type="submit" style="display: block; margin: 0 auto; margin-top: 8px;" >Save</button>
                        </div>
                        <input type="hidden" name="id" value="{{ $welcome_text->id }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="PATCH">
                    </form>
                @endforeach
            </div>
        </div>

    </div>

@endsection
