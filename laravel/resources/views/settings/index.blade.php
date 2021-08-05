@extends('layouts.app')

@section('title', 'Gizmo - Admin: Settings')

@section('breadcrumb')
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
    <div class="card mb-3">
        <div class="card-header font-weight-bold d-flex flex-row">Settings</div>
        <div class="card-body p-0 d-flex flex-wrap justify-content-center flex-column">
            @foreach($settings as $setting)
                <form id="{{ $setting->key }}" class="d-block m-2" action="{{ route('settings.update') }}" method="POST">
                    <label class="font-weight-bolder" for="{{$setting->label}}">{{ $setting->label }}</label>
                    <div class="d-flex flex-row justify-content-between">
                        @if($setting->type != 'text')
                            <input id="{{$setting->label}}" name="value" class="form-control mr-1"
                                   @if($setting->type == 'number') type="number" @endif
                                   value="{{ $setting->value }}"/>
                        @else
                        <textarea id="{{$setting->label}}" name="value" class="form-control mr-1"
                                  style="min-height: 150px;">{{ $setting->value }}</textarea>
                        @endif
                        <div class="button-container d-flex flex-column" style="min-width: 110px">
                            <button class="btn btn-dark" type="submit" style="">Save</button>
                        </div>
                    </div>
                    <input type="hidden" name="id" value="{{ $setting->id }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="PATCH">
                </form>
            @endforeach
        </div>
    </div>

    <div class="modal fade" id="add-video" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel">Add Embedded YouTube Video</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <label class="input-group-text" id="video-addon">URL</label>
                        </div>
                        <input id="video_url" type="url" name="video_url"
                               value="http://www.youtube.com/embed/videoIdHere"
                               class="form-control" aria-describedby="video-addon">
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="add-video-button" type="button" class="btn btn-secondary" data-dismiss="modal">Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            let button = $('#Home2 button')[0];
            const videoButton = document.createElement('button');
            videoButton.classList.add('btn');
            videoButton.classList.add('btn-outline-dark');
            videoButton.style.marginBottom = '4px';
            videoButton.setAttribute('type', 'button');
            videoButton.setAttribute('data-toggle', 'modal');
            videoButton.setAttribute('data-target', '#add-video');
            videoButton.innerText = "Add Video";
            button.parentNode.insertBefore(videoButton, button);
            $('#add-video-button').click(function () {
                const url = $('#video_url')[0].value;
                const textarea = $('#Home2 textarea')[0];
                textarea.innerHTML = '<iframe src="' + url + '" frameborder="0" allowfullscreen></iframe>';
            });
        });
    </script>
@endsection
