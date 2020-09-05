@extends('layouts.app')

@section('title', 'Gizmo - Admin: Error Reports')

@section('breadcrumb')
    <li class="breadcrumb-item active">Error Reports</li>
@endsection

@section('content')
    <div class="btn-group mb-2 d-flex justify-content-center" role="group">
        <a class="btn btn-primary {{ $type == 'new' ? 'active' : ''}}"  href="{{ route('error_report.index', 'new') }}" style="text-decoration: none; color: whitesmoke;">
            New
        </a>
        <a class="btn btn-danger {{ $type == 'decline' ? 'active' : ''}}" href="{{ route('error_report.index', 'decline') }}" style="text-decoration: none; color: whitesmoke;">
            Declined
        </a>
    </div>

    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Error Reports</div>
        <div class="card-body p-0">
            <div class="d-flex justify-content-center mt-2">
                {{ $error_reports->links() }}
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th style="min-width: 60px;">
                            ID
                            <a href="{{ route('error_report.index', array_merge(request()->all(), ['type' => $type, 'sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 140px;">
                            Question ID
                            <a href="{{ route('error_report.index', array_merge(request()->all(), ['type' => $type, 'sort' => 'question_id', 'order' => ((request()->sort == 'question_id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'question_id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'question_id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'question_id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                        </th>
                        <th style="min-width: 140px;">
                            Student ID
                            <a href="{{ route('error_report.index', array_merge(request()->all(), ['type' => $type, 'sort' => 'student_id', 'order' => ((request()->sort == 'student_id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'student_id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'student_id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'student_id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                        </th>
                        <th style="min-width: 120px;">
                            Options
                            <a href="{{ route('error_report.index', array_merge(request()->all(), ['type' => $type, 'sort' => 'options', 'order' => ((request()->sort == 'options' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'options' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'options' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'options' && request()->order == 'desc') ? '-down' : '' }}"></i>
                        </th>
                        <th style="min-width: 140px;">
                            Comment
                            <a href="{{ route('error_report.index', array_merge(request()->all(), ['type' => $type, 'sort' => 'comment', 'order' => ((request()->sort == 'comment' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'comment' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'comment' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'comment' && request()->order == 'desc') ? '-down' : '' }}"></i>
                        </th>
                        <th style="min-width: 140px;">
                            Answer
                            <a href="{{ route('error_report.index', array_merge(request()->all(), ['type' => $type, 'sort' => 'answers', 'order' => ((request()->sort == 'answers' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'answers' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'answers' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'answers' && request()->order == 'desc') ? '-down' : '' }}"></i>
                        </th>
                        <th style="min-width: 160px;">
                            Time
                            <a href="{{ route('error_report.index', array_merge(request()->all(), ['type' => $type, 'sort' => 'created_at', 'order' => ((request()->sort == 'created_at' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'created_at' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'created_at' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'created_at' && request()->order == 'desc') ? '-down' : '' }}"></i>
                        </th>
                        <th style="min-width: 160px;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($error_reports as $error_report)
                        <tr>
                            <td>{{ $error_report->id }}</td>
                            <td><a href="{{ route('questions.show', $error_report->question_id) }}" target="_blank">{{ $error_report->question_id }}</a></td>
                            <td>{{ $error_report->student_id }}</td>
                            <td>{{ $error_report->options }}</td>
                            <td>{{ $error_report->comment }}</td>
                            <td>{{ $error_report->answers }}</td>
                            <td>{{ $error_report->created_at->format('d.m.Y H:i') }}</td>
                            <td class="d-flex flex-row justify-content-end">
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
            <div class="d-flex justify-content-center mt-2">
                {{ $error_reports->links() }}
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .btn-group .active {
            font-weight: bold;
            border-width: 2px;
            border-color: #333333 !important;
        }
    </style>
@endsection
