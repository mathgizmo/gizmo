@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Questions')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('questions.index')  }}">Manage Questions</a></li>
    <li class="breadcrumb-item active">Question Details</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Question Details</div>
        <div class="card-body p-0">
            <div class="row mt-3">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label>Level</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static">{{$question->ltitle}}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label>Unit</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static">{{$question->utitle}}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label>Topic</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static">{{$question->ttitle}}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label>Lesson</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static">{{$question->title}}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label>Question</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static">{!!$question->question!!}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label>Reply Mode</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static">{{$question->reply_mode}}</p>
                </div>
            </div>
            @foreach($answers as $key => $answer)
                <div class="row">
                    <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                        <label>Answer</label>
                    </div>
                    <div class="col-md-8">
                        <p class="form-control-static">{{$answer->value}} {!! $answer->is_correct ? ' - <b>Correct</b>' : ''!!}</p>
                    </div>
                </div>
            @endforeach
            <div class="row">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label>Explanation</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static">{{$question->explanation}}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label>Convert user answer to decimal value</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static">{{ ($question->conversion == true) ? 'Yes' : 'No'}}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 form-control-label ml-3 font-weight-bold">
                    <label>Round user answer with same precision as correct answer</label>
                </div>
                <div class="col-md-8">
                    <p class="form-control-static">{{ ($question->rounding == true) ? 'Yes' : 'No'}}</p>
                </div>
            </div>
            <div class="card-footer">
                <a class="btn btn-secondary" href="{{ route('questions.index') }}">Back</a>
                <a class="btn btn-dark" href="{{ route('questions.edit', $question->id) }}">Edit</a>
                <form action="{{ route('questions.destroy', $question->id) }}"
                      method="POST" style="display: inline;"
                      onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button class="btn btn-danger" type="submit">Delete</button>
                </form>
            </div>

        </div>
    </div>
@endsection

@section('styles')
    <style>
        @media screen and (max-width: 600px) {
            .col-md-8 {
                margin: 0 16px;
            }
        }
    </style>
@endsection
