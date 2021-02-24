<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{{ __('Test Report') }}</title>
    <style>
        .subtitle {
            font-size: 20px;
        }
        .unit {
            margin-left: 16px;
        }
        .topic {
            margin-left: 32px;
        }
        .question {
            margin-left: 48px;
        }
        .level, .unit, .topic, .info {
            font-size: 18px;
        }
        .right-answer {
            background: #dcedc1;
        }
        .wrong-answer {
            background: #ffe4e1;
        }
        .not-answered {
            background: #eeeeee;
        }
    </style>
</head>
<body>
<h1>Test Report</h1>
<div class="subtitle">Test: <strong>{{$test->name}}</strong></div>
<div class="subtitle">Student: <strong>{{$student->email}}</strong></div>
@foreach($attempts as $attempt)
    <h2>Attempt #{{$loop->index}}{{--$attempt->attempt_no--}}</h2>
    <div class="info">Mark: {{round($attempt->mark * 100)}}% @if($attempt->questions_count)
            ({{round($attempt->mark * $attempt->questions_count)}}/{{$attempt->questions_count}}) @endif</div>
    <div class="info">Start At: {{$attempt->start_at}}</div>
    <div class="info">End At: {{$attempt->end_at}}</div>
    <br/>
    @foreach($attempt->levels as $level)
        <div class="levels">
            <div class="level">
                Level: <strong>{{ $level->title }}</strong> -
                <strong>
                    {{round($level->mark * 100)}}%
                    @if($level->total) ({{$level->correct}}/{{$level->total}}) @endif
                </strong>
            </div>
            @foreach($level->units as $unit)
                <div class="units">
                    <div class="unit">
                        Unit: <strong>{{ $unit->title }}</strong> -
                        <strong>
                            {{round($unit->mark * 100)}}%
                            @if($unit->total) ({{$unit->correct}}/{{$unit->total}}) @endif
                        </strong>
                    </div>
                    @foreach($unit->topics as $topic)
                        <div class="topics">
                            <div class="topic">
                                Topic: <strong>{{ $topic->title }}</strong> -
                                <strong>
                                    {{round($topic->mark * 100)}}%
                                    @if($topic->total) ({{$topic->correct}}/{{$topic->total}}) @endif
                                </strong>
                            </div>
                        </div>
                        @foreach($topic->questions as $question)
                            <div class="questions">
                                <div class="question @if(!$question->is_answered) not-answered @elseif(!$question->is_right_answer) wrong-answer @else right-answer @endif">
                                    {{$loop->index + 1}})
                                    <strong>@if(!$question->is_answered) Not Answered @elseif(!$question->is_right_answer) Wrong @else Right @endif</strong>
                                    <em>{!! $question->question !!}</em>
                                    (<strong>{{$question->lesson}}</strong>)
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            @endforeach
        </div>
    @endforeach
@endforeach
</body>
</html>