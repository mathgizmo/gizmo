@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Units')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('units.index')  }}">Manage Units</a></li>
    <li class="breadcrumb-item active">Create Unit</li>
@endsection

@section('content')
    @if(Session::has('flash_message'))
        <div id="successMessage" class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>
            <em> {!! session('flash_message') !!}</em>
        </div>
    @endif
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Create Unit</div>
        <form role="form" action="{{ route('units.store') }}" method="POST">
            <div class="card-body p-0">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group row mt-3 {{ $errors->has('level_id') ? ' has-error' : '' }}">
                    <label for="level_id" class="col-md-2 form-control-label ml-3 font-weight-bold">Module</label>

                    <div class="col-md-8">
                        <select class="form-control" name="level_id" id="level_id">
                            @if (count($levels) > 0)
                                <option value="">Select From ...</option>
                                @foreach($levels as $level)
                                    <option value="{{$level->id}}"
                                            @if (old("level_id") == $level->id) selected="selected"
                                            @endif  @if ($level->id == $lid) selected="selected"
                                            @endif
                                    >{{$level->title}}</option>
                                @endforeach
                            @endif
                        </select>

                        @if ($errors->has('level_id'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('level_id') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('unit_title') ? ' has-error' : '' }}">
                    <label for="unit_title" class="col-md-2 form-control-label ml-3 font-weight-bold">Unit Title</label>

                    <div class="col-md-8">
                        <textarea id="unit_title" class="form-control" name="unit_title"
                                  placeholder="Enter Unit text.."> {{ old('unit_title') }}</textarea>

                        @if ($errors->has('unit_title'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('unit_title') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row mt-3 {{ $errors->has('description') ? ' has-error' : '' }}">
                    <label for="description"
                           class="col-md-2 form-control-label ml-3 font-weight-bold">Description</label>
                    <div class="col-md-8">
                        <textarea type="text" name="description" id="description"
                                  class="form-control">{{ old('description') }}</textarea>
                        @if ($errors->has('description'))
                            <span class="form-text">
                                  <strong>{{ $errors->first('description') }}</strong>
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
                    <label for="type" class="col-md-2 form-control-label ml-3 font-weight-bold">Unit in
                        development</label>
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

                <div class="form-group row{{ $errors->has('order_no') ? ' has-error' : '' }}">
                    <label for="order_no" class="col-md-2 form-control-label ml-3 font-weight-bold">Order No</label>

                    <div class="col-md-8">
                        <select class="form-control" name="order_no" id="order_no">
                            <option value="1">1</option>
                            @if ($total_unit > 0)
                                @for($count = 2; $count <= $total_unit + 1; $count++)
                                    <option
                                        <?php echo ($count > $total_unit) ? 'selected="selected"' : ''; ?> value="{{$count}}">{{$count}}</option>
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

                <div class="form-group row">
                    <label class="col-md-2 form-control-label ml-3 font-weight-bold">Tags</label>
                    <div class="col-md-8">
                        <div id="tag-filter-group" class="d-flex flex-wrap">
                            @foreach($tags as $tag)
                                <span class="badge badge-pill tag-filter-badge badge-light m-1"
                                      data-tag-id="{{ $tag->id }}" style="cursor:pointer;user-select:none;">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                        <input type="hidden" name="tag_id" id="tag_id_hidden" value="">
                        <small class="form-text text-muted">Click to select or unselect tags. Multiple selection supported.</small>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a class="btn btn-secondary" href="{{ route('units.index') }}">Back</a>
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
            }, 4000);

            // Tag filter badge click handler
            $(document).on('click', '.tag-filter-badge', function() {
                let tagId = $(this).data('tag-id').toString();
                let selected = $('#tag_id_hidden').val().split(',').filter(Boolean);
                if ($(this).hasClass('badge-primary')) {
                    // Deselect
                    selected = selected.filter(id => id !== tagId);
                    $(this).removeClass('badge-primary').addClass('badge-light');
                } else {
                    // Select
                    selected.push(tagId);
                    $(this).removeClass('badge-light').addClass('badge-primary');
                }
                // Remove duplicates
                selected = [...new Set(selected)];
                $('#tag_id_hidden').val(selected.join(','));
            });

            // On form submit, convert tag_id_hidden to hidden inputs
            $('form[role="form"]').on('submit', function(e) {
                let selected = $('#tag_id_hidden').val().split(',').filter(Boolean);
                // Remove any previous tag_id[]
                $(this).find('input[name="tag_id[]"]').remove();
                selected.forEach(function(id) {
                    $('<input>').attr({type: 'hidden', name: 'tag_id[]', value: id}).appendTo('form[role="form"]');
                });
            });
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

        .tag-filter-badge {
            border: 1px solid #007bff;
            color: #007bff;
            background: #fff;
            transition: all 0.2s;
        }
        .tag-filter-badge.badge-primary {
            background: #007bff;
            color: #fff;
        }
        .tag-filter-badge.badge-light {
            background: #fff;
            color: #007bff;
        }
    </style>
@endsection
