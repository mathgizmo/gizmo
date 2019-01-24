@extends('layouts.app')

@section('content')

    <div class="container">
        <ul class="nav nav-pills nav-justified">
            <li class="{{ $type == 'new' ? 'active' : ''}}"><a href="{{ route('error_report.index', 'new') }}">New</a></li>
            <li class="{{ $type == 'decline' ? 'active' : ''}}"><a href="{{ route('error_report.index', 'decline') }}">Declined</a></li>
        </ul>
        <br>
        <div class="panel panel-default">
            <div class="panel-heading">Error report</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        {{ $error_reports->links() }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="col-md">
                                        ID
                                        <a href="{{ route('error_report.index', array_merge(request()->all(), ['type' => $type, 'sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                        </a>
                                    </th>
                                    <th class="col-md">
                                        Question ID
                                        <a href="{{ route('error_report.index', array_merge(request()->all(), ['type' => $type, 'sort' => 'question_id', 'order' => ((request()->sort == 'question_id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'question_id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'question_id' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'question_id' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                    </th>
                                    <th class="col-md">
                                        Student ID
                                        <a href="{{ route('error_report.index', array_merge(request()->all(), ['type' => $type, 'sort' => 'student_id', 'order' => ((request()->sort == 'student_id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'student_id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'student_id' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'student_id' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                    </th>
                                    <th class="col-md">
                                        Options
                                        <a href="{{ route('error_report.index', array_merge(request()->all(), ['type' => $type, 'sort' => 'options', 'order' => ((request()->sort == 'options' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'options' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'options' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'options' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                    </th>
                                    <th class="col-md">
                                        Comment
                                        <a href="{{ route('error_report.index', array_merge(request()->all(), ['type' => $type, 'sort' => 'comment', 'order' => ((request()->sort == 'comment' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'comment' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'comment' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'comment' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                    </th>
                                    <th class="col-md">
                                        Answer
                                        <a href="{{ route('error_report.index', array_merge(request()->all(), ['type' => $type, 'sort' => 'answers', 'order' => ((request()->sort == 'answers' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'answers' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'answers' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'answers' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                    </th>
                                    <th class="col-md">
                                        Time
                                        <a href="{{ route('error_report.index', array_merge(request()->all(), ['type' => $type, 'sort' => 'created_at', 'order' => ((request()->sort == 'created_at' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'created_at' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'created_at' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'created_at' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                    </th>
                                    <th class="col-md"></th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($error_reports as $error_report)
                                        <tr>
                                            <td>{{ $error_report->id }}</td>
                                            <td><a href="{{ route('question_views.show', $error_report->question_id) }}" target="_blank">{{ $error_report->question_id }}</a></td>
                                            <td>{{ $error_report->student_id }}</td>
                                            <td>{{ $error_report->options }}</td>
                                            <td>{{ $error_report->comment }}</td>
                                            <td>{{ $error_report->answers }}</td>
                                            <td>{{ $error_report->created_at->format('d.m.Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    @if ($error_report->declined == 1)
                                                        <a href="{{ route('error_report.update_status', ['type' => 'new', 'id' => $error_report->id]) }}" class="btn btn-info">Set new</a>
                                                    @else
                                                        <a href="{{ route('error_report.update_status', ['type' => 'decline', 'id' => $error_report->id]) }}" class="btn btn-info">Set decline</a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {{ $error_reports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
