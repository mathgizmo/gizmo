@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Modules')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('levels.index')  }}">Manage Modules</a></li>
    <li class="breadcrumb-item active">Create Module</li>
@endsection

@section('content')
    @if(Session::has('flash_message'))
        <div id="successMessage" class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>
            <em> {!! session('flash_message') !!}</em>
        </div>
    @endif
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Create Module</div>
        <form role="form" action="{{ route('levels.store') }}" method="POST">
            <div class="card-body p-0">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group row mt-3 {{ $errors->has('title') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-2 form-control-label ml-3 font-weight-bold">Title</label>
                    <div class="col-md-8">
                        <textarea id="title" class="form-control" name="title" placeholder="Enter Title">{{ old('title') }}</textarea>
                        @if ($errors->has('title'))
                            <span class="form-text">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row mt-3 {{ $errors->has('description') ? ' has-error' : '' }}">
                    <label for="description" class="col-md-2 form-control-label ml-3 font-weight-bold">Description</label>
                    <div class="col-md-8">
                        <textarea type="text" name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                        @if ($errors->has('description'))
                            <span class="form-text">
                                  <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row{{ $errors->has('order_no') ? ' has-error' : '' }}">
                    <label for="order_no" class="col-md-2 form-control-label ml-3 font-weight-bold">Order No</label>
                    <div class="col-md-8">
                        <select class="form-control" name="order_no" id="order_no">
                            <option value="1">1</option>
                            @if ($total_level > 0)
                                @for($count = 2; $count <= $total_level + 1; $count++)
                                    <option
                                        <?php echo ($count > $total_level) ? 'selected="selected"' : ''; ?> value="{{$count}}">{{$count}}</option>
                                @endfor
                            @endif
                        </select>

                        @if ($errors->has('order_no'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('order_no') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row{{ $errors->has('dependency') ? ' has-error' : '' }}">
                    <label for="type" class="col-md-2 form-control-label ml-3 font-weight-bold">This should be finished
                        to continue</label>

                    <div class="col-md-8 radio">
                        <label for="type" class="col-md-3"> <input checked="checked" type="checkbox" name="dependency"
                                                                   value="1"></label>
                        @if ($errors->has('dependency'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('dependency') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row{{ $errors->has('dev_mode') ? ' has-error' : '' }}">
                    <label for="type" class="col-md-2 form-control-label ml-3 font-weight-bold">Module in development</label>
                    <div class="col-md-8 radio">
                        <label for="type" class="col-md-3"> <input checked="checked" type="checkbox" name="dev_mode"
                                                                   value="1"></label>
                        @if ($errors->has('dev_mode'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('dev_mode') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a class="btn btn-secondary" href="{{ route('levels.index') }}">Back</a>
                <button class="btn btn-dark" type="submit">Submit</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
    <script>
        $(document).ready(function () {
            CKEDITOR.replace('description', {toolbar: [['Bold', 'Italic', 'Font', 'FontSize']]});
            setTimeout(function () {
                $('#successMessage').fadeOut('fast');
            }, 4000); // <-- time in milliseconds
        });
    </script>
@endsection

@section('styles')
    <style>
        .cke_contents {
            min-height: 360px !important;
        }
        @media screen and (max-width: 600px) {
            .col-md-8 {
                margin: 0 16px;
            }
        }
    </style>
@endsection
