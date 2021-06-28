@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Tutorials')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tutorials.index')  }}">Manage Tutorials</a></li>
    <li class="breadcrumb-item active">Edit Tutorial</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">
            Edit Tutorial
        </div>
        <form role="form"
              action="{{ route('tutorials.update', $tutorial->id) }}" method="POST">
            <div class="card-body p-0">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group row mt-3 {{ $errors->has('title') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-2 form-control-label ml-3 font-weight-bold"> Title</label>
                    <div class="col-md-8">
                       <textarea id="title" class="form-control" name="title">{{$tutorial->title}}</textarea>
                        @if ($errors->has('title'))
                            <span class="form-text">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="editor form-group row mt-3 {{ $errors->has('data') ? ' has-error' : '' }}">
                    <label for="data" class="col-md-2 form-control-label ml-3 font-weight-bold">Data</label>
                    <div class="col-md-8">
                        <textarea id="data" class="form-control" name="data">{{$tutorial->data}}</textarea>
                        @if ($errors->has('data'))
                            <span class="form-text">
                                <strong>{{ $errors->first('data') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('order_no') ? ' has-error' : '' }}">
                    <label for="order_no" class="col-md-2 form-control-label ml-3 font-weight-bold">Order No</label>
                    <div class="col-md-8">
                        <select class="form-control" name="order_no" id="order_no">
                            <option value="1">1</option>
                            @if ($total_tutorials > 0)
                                @for($count = 2; $count <= $total_tutorials + 1; $count++)
                                    <option <?php echo ($count == $tutorial->order_no) ? 'selected="selected"' : ''; ?> value="{{$count}}">{{$count}}</option>
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

                <div class="form-group row{{ $errors->has('is_for_student') ? ' has-error' : '' }}">
                    <label for="is_for_student" class="col-md-2 form-control-label ml-3 font-weight-bold">For students</label>
                    <div class="col-md-6 radio">
                        <label for="is_for_student" class="col-md-3"> <input
                                    {{ $tutorial->is_for_student ? 'checked="checked"' : ''}} type="checkbox"
                                    name="is_for_student" value="1"></label>
                        @if ($errors->has('is_for_student'))
                            <span class="form-text">
                                <strong>{{ $errors->first('is_for_student') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row{{ $errors->has('is_for_teacher') ? ' has-error' : '' }}">
                    <label for="is_for_teacher" class="col-md-2 form-control-label ml-3 font-weight-bold">For teachers</label>
                    <div class="col-md-6 radio">
                        <label for="is_for_teacher" class="col-md-3"> <input
                                    {{ $tutorial->is_for_teacher ? 'checked="checked"' : ''}} type="checkbox"
                                    name="is_for_teacher" value="1"></label>
                        @if ($errors->has('is_for_teacher'))
                            <span class="form-text">
                                <strong>{{ $errors->first('is_for_teacher') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <a class="btn btn-secondary"
                   href="{{ route('tutorials.index') }}">Back</a>
                <button class="btn btn-dark" type="submit">Update</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('js/jquery.jslatex.packed.js') }}"></script>
    <script src="{{ URL::asset('js/ckeditor/ckeditor.js') }}"></script>
    <script>
        const data = CKEDITOR.replace('data', {
            customConfig: '/admin/js/ckeditor/config_for_tutorials.js'
        });
    </script>
@endsection

@section('styles')
    <style>
        .editor, .cke {
            min-height: 500px;
        }
        #editor {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            margin: 0 12px;
            font-size: 14px;
            word-wrap: break-word;
        }
        @media screen and (max-width: 600px) {
            .col-md-8 {
                margin: 0 16px;
            }
        }
    </style>
@endsection
