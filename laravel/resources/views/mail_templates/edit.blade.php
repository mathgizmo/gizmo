@extends('layouts.app')

@section('title', 'Gizmo - Admin: Mails')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('mails.index')  }}">Mails</a></li>
    <li class="breadcrumb-item active">Edit Mail</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">@lang('settings.edit_mail_template_title')</div>
        <form role="form" action="{{ route('mails.update', ['mail' => $mail]) }}" autocomplete="off" method="POST">
            @csrf
            @method('put')
            <div class="card-body p-0">
                <div class="form-group row mt-3 {{ $errors->has('subject') ? ' has-error' : '' }}">
                    <label for="subject" class="col-md-2 form-control-label ml-3 font-weight-bold">@lang('settings.subject')</label>
                    <div class="col-md-8">
                        <input type="text" name="subject" id="subject" class="form-control"
                               value="{{ old('subject', $mail->subject) }}">
                        @if ($errors->has('subject'))
                            <span class="form-text">
                                  <strong>{{ $errors->first('subject') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row mt-3">
                    <label class="col-md-2 form-control-label ml-3 font-weight-bold">@lang('settings.available_variables')</label>
                    <div class="col-md-8">
                        <div class="d-flex flex-row flex-wrap">
                            @foreach($available_variables as $variable)
                                <span class="variable mb-1 mr-1"> {{ $variable }}</span>
                            @endforeach
                        </div>
                        <label class="info mt-2">@lang('settings.variables_usage')</label>
                    </div>

                </div>
                <div class="form-group row mt-3 {{ $errors->has('body') ? ' has-error' : '' }}">
                    <label for="body" class="col-md-2 form-control-label ml-3 font-weight-bold">@lang('settings.body')</label>
                    <div class="col-md-8">
                        <textarea type="text" name="body" id="body" class="form-control">{{ old('body', $mail->body) }}</textarea>
                        @if ($errors->has('body'))
                            <span class="form-text">
                                  <strong>{{ $errors->first('body') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a class="btn btn-secondary"
                   href="{{ route('mails.index') }}">Back</a>
                <button class="btn btn-dark" type="submit">Submit</button>
            </div>
        </form>
    </div>
@endsection

@section('styles')
    <style>
        .ck-content {
            min-height: 400px !important;
        }
        .info {
            background-color: #f0f7fb;
            border-left: solid 4px #3498db;
            line-height: 18px;
            padding: 10px;
            width: 100%;
        }
        .variable {
            font-weight: bolder;
            background-color: rgba(0, 0, 0, 0.03);
            padding: 8px;
        }
    </style>
@endsection

@section('scripts')
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
    <script>
        $(document).ready(function () {
            CKEDITOR.replace('body', { toolbar : [ ['Bold','Italic','Font','FontSize'] ] });
        });
    </script>
@endsection
