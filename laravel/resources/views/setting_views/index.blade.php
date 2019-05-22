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
                        <div class="col-md-4 form-group" id="{{ $welcome_text->key }}">
                            <label class="control-label text-center" style="width: 100%;">{{ $welcome_text->label }}</label>
                            <textarea name="value" class="form-control" style="min-height: 150px; margin-top: 8px;">{{ $welcome_text->value }}</textarea>
                            <div style="display: flex; flex-direction: row; justify-content: center;">
                                <button class="btn btn-primary" type="submit" style="margin: 4px;">Save</button>
                            </div>
                        </div>
                        <input type="hidden" name="id" value="{{ $welcome_text->id }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="PATCH">
                    </form>
                @endforeach
            </div>
        </div>

    </div>

    <div class="modal fade" id="add-video" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modalLabel">Add Embedded YouTube Video</h4>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <span class="input-group-addon" id="video-addon">URL</span>
                        <input id="video_url" type="url" name="video_url" value="http://www.youtube.com/embed/videoIdHere"
                               class="form-control" aria-describedby="video-addon">
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="add-video-button" type="button" class="btn btn-secondary" data-dismiss="modal">Submit</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    $( document ).ready(function() {
        let button = $('#Home2 button')[0];
        const videoButton = document.createElement("button");
        videoButton.classList.add('btn');
        videoButton.classList.add('btn-primary');
        videoButton.style.margin = '4px';
        videoButton.setAttribute('type', 'button');
        videoButton.setAttribute('data-toggle', 'modal');
        videoButton.setAttribute('data-target', '#add-video');
        videoButton.innerText = "Add Video";
        button.parentNode.insertBefore(videoButton, button);
        $('#add-video-button').click(function() {
            const url = $('#video_url')[0].value;
            const textarea = $('#Home2 textarea')[0];
            textarea.innerHTML = '<iframe src="'+url+'" frameborder="0" allowfullscreen></iframe>';
        });
    });
</script>
@endsection
