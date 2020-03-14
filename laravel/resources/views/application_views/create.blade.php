@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            Application / Create
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form id="application" class="form-horizontal" role="form"
                                      action="{{ route('application_views.store') }}" method="POST">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label for="name" class="col-md-4 control-label">Name</label>
                                        <div class="col-md-6">
                                            <input id="name" type="text" class="form-control" name="name">
                                            @if ($errors->has('name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('icon') ? ' has-error' : '' }}">
                                        <label for="icon" class="col-md-4 control-label">Icon</label>
                                        <div class="col-md-6">
                                            <label id="change-image" style="display: none;">
                                                <img class="show-img" src="{{ URL::asset('images/default-icon.svg') }}"
                                                     width="100px"/>
                                                <a href="#addImageModal" class="btn" data-toggle="modal"
                                                   data-target="#addImageModal">Change Icon</a>
                                            </label>
                                            <label id="add-image">
                                                <a href="#addImageModal" class="btn" data-toggle="modal" data-target="#addImageModal">Add Icon</a>
                                            </label>
                                            <input type="hidden" name="icon" value="">
                                        </div>
                                    </div>
                                    <div class="tree m-0 p-0">
                                        <ul>
                                            @foreach($tree as $level)
                                                <li>
                                                    <input type="checkbox" name="level[{{$level->id}}]" {{$level->checked ? 'checked="checked"' : '' }} />
                                                    <label>{{$level->text}}</label>
                                                    <ul class="collapse">
                                                        @foreach($level->children as $unit)
                                                        <li>
                                                            <input type="checkbox" name="unit[{{$unit->id}}]" {{$unit->checked ? 'checked="checked"' : '' }} />
                                                            <label>{{$unit->text}}</label>
                                                            <ul class="collapse">
                                                                @foreach($unit->children as $topic)
                                                                    <li>
                                                                        <input type="checkbox" name="topic[{{$topic->id}}]" {{$topic->checked ? 'checked="checked"' : '' }} />
                                                                        <label>{{$topic->text}}</label>
                                                                        <ul class="collapse">
                                                                            @foreach($topic->children as $lesson)
                                                                                <li>
                                                                                    <input type="checkbox" name="lesson[{{$lesson->id}}]" {{$lesson->checked ? 'checked="checked"' : '' }} />
                                                                                    <label>{{$lesson->text}}</label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-4">
                                            <a class="btn btn-default" href="{{ route('application_views.index') }}">Back</a>
                                            <button class="btn btn-primary" type="submit">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addImageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Add Application Icon</h4>
                </div>
                <div class="modal-body">
                    <div style="display: flex; flex-direction: row; justify-content: center;">
                        <img id="custom-img" src="{{ URL::asset('images/default-icon.svg') }}"
                             style='z-index: 999; margin: 4px; height: 100px; width: 100px;'/>
                        <span style="margin: 4px;">
                            <span>Choose application icon</span>
                            <input type="file" name="icon" accept=".SVG" class="form-control-file" id="upload-icon"
                                   style="margin: 4px;">
                            <button class="btn btn-primary" id='upload-icon-button'
                                    style="text-align:center; margin-top: 4px;">Upload Icon</button>
                        </span>
                    </div>

                    <script type="text/javascript">
                        function checkIcon(checkedIcon) {
                            let modal = document.getElementById('topic-icons-list');
                            let inputs = modal.getElementsByTagName('input');
                            for (var i = 0; i < inputs.length; i++) {
                                if (inputs[i].type == "checkbox") {
                                    inputs[i].checked = false;
                                }
                            }
                            checkedIcon.checked = true;
                        }
                    </script>
                    <div class="application-images">
                        <ul id='topic-icons-list'>
                            @for ($i = 0; $i < count($icons); $i++)
                                <li><input type="checkbox" id="cb{{ $i }}" value="{{$icons[$i]}}"
                                           onclick="checkIcon(this)"/>
                                    <label for="cb{{ $i }}">
                                        <img id="{{$icons[$i]}}" src="{{ URL::asset($icons[$i]) }}"
                                             class='topic-icon'/>
                                    </label>
                                </li>
                            @endfor
                        </ul>
                    </div>

                </div>
                <div class="modal-footer">
                    <script>
                        function deleteIcon() {
                            let modal = document.getElementById('topic-icons-list');
                            let inputs = modal.getElementsByTagName('input');
                            let icon = '';

                            for (var i = 0; i < inputs.length; i++) {
                                if (inputs[i].type == "checkbox" && inputs[i].checked) {
                                    icon = inputs[i].value;
                                    inputs[i].parentNode.remove();
                                }
                            }
                            let formData = new FormData();
                            formData.append('icon', icon);
                            formData.append('_token', "{{ csrf_token() }}");
                            $.ajax({
                                url: "{{ route('file.delete-icon') }}",
                                type: "POST",
                                data: formData,
                                cache: false,
                                dataType: 'json',
                                processData: false,
                                contentType: false,
                                success: function (data, textStatus, jqXHR) {
                                    //console.log(data);
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    console.log('ERROR');
                                }
                            });
                        }
                    </script>
                    <button id="delete-image" type="button" data-toggle="confirmation" data-placement="top"
                            data-on-confirm="deleteIcon()" class="btn btn-danger pull-left">Delete Icon
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="save-image" type="button" class="btn btn-primary">Attach Icon</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('styles')
    <style>
        .tree ul {
            list-style: none;
            margin: 5px 20px;
        }
        .tree li {
            margin: 10px 0;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $('ul label').click(function () {
            $(this).next().toggleClass('collapse');
        });

        $('input[type="checkbox"]').change(function(e) {
            var checked = $(this).prop("checked"),
                container = $(this).parent(),
                siblings = container.siblings();
            container.find('input[type="checkbox"]').prop({
                indeterminate: false,
                checked: checked
            });
            // $(this).next().val(checked ? 1 : 0);

            function checkSiblings(el) {
                var parent = el.parent().parent(),
                    all = true;
                el.siblings().each(function() {
                    let returnValue = all = ($(this).children('input[type="checkbox"]').prop("checked") === checked);
                    return returnValue;
                });
                if (all && checked) {
                    parent.children('input[type="checkbox"]').prop({
                        indeterminate: false,
                        checked: checked
                    });
                    checkSiblings(parent);
                } else if (all && !checked) {
                    parent.children('input[type="checkbox"]').prop("checked", checked);
                    parent.children('input[type="checkbox"]').prop("indeterminate", (parent.find('input[type="checkbox"]:checked').length > 0));
                    checkSiblings(parent);
                } else {
                    el.parents("li").children('input[type="checkbox"]').prop({
                        indeterminate: true,
                        checked: false
                    });
                }
            }
            checkSiblings(container);
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#upload-icon').change(function () {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        let img = document.getElementById('custom-img');
                        img.setAttribute('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });

            $('#upload-icon-button').click(function () {
                let icons = document.getElementById('upload-icon').files;
                if (icons.length == 0) {
                    alert('Please, choose icons!');
                    return;
                }
                if (icons[0].type != 'image/svg+xml') {
                    alert('Invalid type of file. The file must be image/svg+xml, not ' + icons[0].type);
                    return;
                }
                let formData = new FormData();
                formData.append('icon', icons[0]);
                formData.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('file.upload-icon') }}",
                    type: "POST",
                    data: formData,
                    cache: false,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function (data, textStatus, jqXHR) {
                        $('#topic-icons-list')
                            .load('{{ route('application_views.create') }} #topic-icons-list > *');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log('textStatus: ' + textStatus + '\njqXHR:' + jqXHR + '\errorThrown:' + errorThrown);
                    }
                });
            });

            $(function () {
                $("#addImageModal button#save-image").on('click', function () {
                    $('#addImageModal').modal('hide');
                    $('form#application label#change-image img').removeClass();

                    $('form#application label#add-image').hide();
                    $('form#application label#change-image').show();

                    var applicationIcon = $('#addImageModal input[type=checkbox]:checked').val();
                    $('form#application label#change-image img').addClass(applicationIcon);
                    $('form#application input[name=icon').val(applicationIcon);
                    $('form#application label#change-image img').attr("src", "{{ URL::asset('/') }}" + applicationIcon);
                });
            });
        });
    </script>

@endsection
