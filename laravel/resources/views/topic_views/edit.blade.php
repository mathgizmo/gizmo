@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><div class="panel-title">
             Edit Topic
            </div></div>

                <div class="panel-body">

    <div class="row">
        <div class="col-md-12">

            <form id="topic" class="form-horizontal" role="form" action="{{ route('topic_views.update', $topic->id) }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                    <label for="level_id" class="col-md-4 control-label">Level</label>

                    <div class="col-md-6">
                          <select class="form-control" name="level_id" id="level_id">

                          @if (count($levels) > 0)
                              <option value="">Select From ...</option>
                                @foreach($levels as $level)
                                    <option value="{{$level->id}}" @if (old("level_id") == $level->id) selected="selected" @endif  @if ($level->id == $topic->lid) selected="selected"
                                    @endif
                                    >{{$level->title}}</option>
                                @endforeach
                         @endif
                            </select>

                        @if ($errors->has('level_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('level_id') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div id="unit_options"> </div>
                    <div class="form-group{{ $errors->has('unit_id') ? ' has-error' : '' }}">
                        <label for="unit_id" class="col-md-4 control-label">Unit</label>

                        <div class="col-md-6">
                              <select class="form-control" name="unit_id" id="unit_id">
                               @if (count($units) > 0)
                              @foreach($units as $unit)
                                        <option value="{{$unit->id}}" @if ($unit->id == $topic->uid) selected="selected"
                                        @endif
                                        >{{$unit->title}}</option>
                                    @endforeach
                              @endif
                                </select>

                            @if ($errors->has('unit_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('unit_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('topic_title') ? ' has-error' : '' }}">
                        <label for="topic_title" class="col-md-4 control-label"> Title</label>

                        <div class="col-md-6">
                            <input id="topic_title" type="text" class="form-control"  name="topic_title" placeholder="Enter Topic text.." value="{{$topic->title}}">

                            @if ($errors->has('topic_title'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('topic_title') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('short_name') ? ' has-error' : '' }}">
                            <label for="short_name" class="col-md-4 control-label">Short Name</label>

                            <div class="col-md-6">
                                <input id="short_name" type="text" class="form-control"  name="short_name" placeholder="Enter Short Name" value="{{ $topic->short_name }}">

                                @if ($errors->has('short_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('short_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                      </div>

                    <div class="form-group{{ $errors->has('icon_src') ? ' has-error' : '' }}">
                        <label for="icon_src" class="col-md-4 control-label">Image</label>

                        <div class="col-md-6">
                            <label id="change-image">
                                <img class="show-img" src="{{ URL::asset($topic->icon_src) }}" width="100px" />
                                <a href="#addImageModal" class="btn" data-toggle="modal" data-target="#addImageModal">Change Image</a>
                            </label>
                            <input type="hidden" name="icon_src" value="">
                        </div>
                    </div>
                <div class="form-group{{ $errors->has('dependency') ? ' has-error' : '' }}">
                    <label for="type" class="col-md-4 control-label">This should be finished to continue</label>

                    <div class="col-md-6 radio">
                        <label for="type" class="col-md-3"> <input {{ ($topic->dependency == true) ? 'checked="checked"' : ''}} type="checkbox" name="dependency" value="1"></label>
                        @if ($errors->has('dependency'))
                            <span class="help-block">
                                <strong>{{ $errors->first('dependency') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('dev_mode') ? ' has-error' : '' }}">
                    <label for="type" class="col-md-4 control-label">Topic in development</label>
                    <div class="col-md-6 radio">
                        <label for="type" class="col-md-3"> <input {{ ($topic->dev_mode == true) ? 'checked="checked"' : ''}} type="checkbox" name="dev_mode" value="1"></label>
                        @if ($errors->has('dev_mode'))
                            <span class="help-block">
                                <strong>{{ $errors->first('dev_mode') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                    <div class="form-group{{ $errors->has('order_no') ? ' has-error' : '' }}">
                        <label for="order_no" class="col-md-4 control-label">Order No</label>

                        <div class="col-md-6">
                              <select class="form-control" name="order_no" id="order_no">
                                <option value="1">1</option>
                                @if ($total_topic > 0)
                                    @for($count = 2; $count <= $total_topic + 1; $count++)
                                        <option <?php echo ($count == $topic->order_no) ? 'selected="selected"' : ''; ?> value="{{$count}}">{{$count}}</option>
                                    @endfor
                                @endif
                                </select>

                            @if ($errors->has('order_no'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('order_no') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <a class="btn btn-default" href="{{ route('topic_views.create') }}">Back</a>
                            <button class="btn btn-primary" type="submit" >Update</button>

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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Topic Image</h4>
            </div>
            <div class="modal-body">
                
                <div style="display: flex; flex-direction: row; justify-content: center;">
                    <img id="custom-img" src="{{ URL::asset('images/default-icon.svg') }}" style='z-index: 999; margin: 4px; height: 100px; width: 100px;'/>
                    <span style="margin: 4px;">
                        <span>Choose topic icon</span>
                        <input type="file" name="icon" accept=".SVG" class="form-control-file" id="upload-icon" style="margin: 4px;">
                        <button class="btn btn-primary" id='upload-icon-button' style="text-align:center; margin-top: 4px;">Upload Icon</button>
                    </span>
                </div>
                
                <script type="text/javascript">
                    function checkIcon(checkedIcon) {
                        let modal = document.getElementById('topic-icons-list');
                        let inputs = modal.getElementsByTagName('input');
                        for(var i = 0; i < inputs.length; i++) {
                            if(inputs[i].type == "checkbox") {
                                inputs[i].checked = false; 
                            }  
                        }
                        checkedIcon.checked = true; 
                    }
                </script>
                <div class="topic-images">
                    <ul id='topic-icons-list'>
                        @for ($i = 0; $i < count($icons); $i++)
                        <li><input type="checkbox" id="cb{{ $i }}" value="{{$icons[$i]}}" onclick="checkIcon(this)" />
                            <label for="cb{{ $i }}">
                                <img id="{{$icons[$i]}}" src="{{ URL::asset($icons[$i]) }}" class='topic-icon'/>
                            <!-- @if (file_exists(substr($icons[$i], 0, -4).'-gold.svg')) 
                                <img id="{{substr($icons[$i], 0, -4).'-gold.svg'}}" src="{{ URL::asset(substr($icons[$i], 0, -4).'-complete.svg') }}" class='topic-icon'/>
                            @endif -->
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

                        for(var i = 0; i < inputs.length; i++) {
                            if(inputs[i].type == "checkbox" && inputs[i].checked) {
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
                            success: function(data, textStatus, jqXHR) {
                                //console.log(data);
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log('ERROR');
                            }
                        });
                    }
                </script>
                <button id="delete-image" type="button" data-toggle="confirmation" data-placement="top" data-on-confirm="deleteIcon()" class="btn btn-danger pull-left" >Delete Image</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="save-image" type="button" class="btn btn-primary">Attach Image</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
$(document).ready(function(){
    $('#upload-icon').change( function() {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          let img = document.getElementById('custom-img');
          img.setAttribute('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
      }
    });

    $('#upload-icon-button').click( function() {
        let icons = document.getElementById('upload-icon').files;
        if(icons.length == 0) {
            alert('Please, choose icons!');
            return;
        }
        if(icons[0].type != 'image/svg+xml') {
            alert('Invalid type of file. The file must be image/svg+xml, not '+icons[0].type);
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
            success: function(data, textStatus, jqXHR) {
                $('#topic-icons-list')
                    .load('{{ route('topic_views.create') }} #topic-icons-list > *');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('textStatus: ' + textStatus + '\njqXHR:' +jqXHR + '\errorThrown:' + errorThrown);
            }
        });
    });

    $(function () {
        $("#addImageModal button#save-image").on('click', function() {
            $('#addImageModal').modal('hide');
            $('form#topic label#change-image img').removeClass();

            $('form#topic label#add-image').hide();
            $('form#topic label#change-image').show();

            var topicIcon = $('#addImageModal input[type=checkbox]:checked').val();
            $('form#topic label#change-image img').addClass(topicIcon);
            $('form#topic input[name=icon_src').val(topicIcon);
            $('form#topic label#change-image img').attr("src", "{{ URL::asset('/') }}"+topicIcon);
        });
    });
});
</script>

@endsection
