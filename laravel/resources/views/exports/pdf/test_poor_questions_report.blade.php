<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{{ __('Test poorly answered questions report') }}</title>
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
<h1>Test poorly answered questions report</h1>
<div class="subtitle">Test: <strong>{{$test->name}}</strong></div>
</body>
</html>