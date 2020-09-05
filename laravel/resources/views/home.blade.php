@extends('layouts.app')

@section('title', 'Gizmo - Admin: Home')

@section('breadcrumb')
    <li class="breadcrumb-item active">Home Page</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom">
        <h4 class="h4">Content Management</h4>
    </div>
    <div class="actions-container d-flex flex-wrap align-items-center mt-2">
        @if(auth()->user()->isAdmin() || auth()->user()->isQuestionsEditor())
            <a class="btn btn-outline-dark mr-2 mb-2" href="{{ Route('questions.create') }}">
                Create Question
            </a>
            <a class="btn btn-outline-dark mr-2 mb-2" href="{{ url('/questions') }}">
                Manage Question
            </a>
        @endif
        @if(auth()->user()->isAdmin())
            <a class="btn btn-outline-dark mr-2 mb-2" href="{{ url('/lessons') }}">
                Manage Lessons
            </a>
            <a class="btn btn-outline-dark mr-2 mb-2" href="{{ url('/topics') }}">
                Manage Topics
            </a>
            <a class="btn btn-outline-dark mr-2 mb-2" href="{{ url('/units') }}">
                Manage Units
            </a>
            <a class="btn btn-outline-dark mr-2 mb-2" href="{{ url('/levels') }}">
                Manage Modules
            </a>
            {{-- <a class="btn btn-outline-dark mr-2 mb-2" href="{{ url('/placements') }}">
                Manage Placements
            </a> --}}
            <a class="btn btn-outline-dark mr-2 mb-2" href="{{ route('error_report.index', 'new') }}">
                Error Report
            </a>
        @endif
    </div>
    @if(auth()->user()->isAdmin())
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom mt-2">
        <h4 class="h4">Class Management</h4>
    </div>
    <div class="actions-container d-flex flex-wrap align-items-center mt-2">
        <a class="btn btn-outline-dark mr-2" href="{{ route('students.index') }}">
            Participants
        </a>
        <a class="btn btn-outline-dark mr-2" href="{{ url('/applications') }}">
            Manage Assignments
        </a>
        <a class="btn btn-outline-dark mr-2" href="{{ url('/classes') }}">
            Manage Classes
        </a>
    </div>
    @endif
    @if(auth()->user()->isSuperAdmin())
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom mt-2">
            <h4 class="h4">Settings</h4>
        </div>
        <div class="actions-container d-flex flex-wrap align-items-center mt-2">
            <a class="btn btn-outline-dark mr-2" href="{{ route('mails.index') }}">
                Mails
            </a>
            <a class="btn btn-outline-dark mr-2" href="{{ route('users.index') }}">
                Administrators
            </a>
            <a class="btn btn-outline-dark mr-2" href="{{ url('/dashboards') }}">
                Dashboards
            </a>
            <a class="btn btn-outline-dark mr-2" href="{{ route('settings.index') }}">
                Settings
            </a>
        </div>
    @endif
    @if(auth()->user()->isSuperAdmin())
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom mt-2">
            <h4 class="h4">Actions</h4>
        </div>
        <div class="actions-container d-flex flex-wrap align-items-center mt-2">
            <button id="generate-detailed-reports-button" class="btn btn-dark mr-2" type="button">
                Generate Detailed Reports for All Classes
            </button>
            <button id="delete-answers-statistics-button" class="btn btn-dark mr-2" type="button">
                Delete old answers statistics data
            </button>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        $('#generate-detailed-reports-button').click(function() {
            $("#generate-detailed-reports-button").attr("disabled", true);
            $.ajax({
                url: "{{route('job.reports.class-detailed.generate')}}",
                type: "GET",
                success: function (data, textStatus, jqXHR) {
                    alert('Generated!');
                }
            });
        });
        $('#delete-answers-statistics-button').click(function() {
            $("#delete-answers-statistics-button").attr("disabled", true);
            $.ajax({
                url: "{{route('job.statistics.answers.delete')}}",
                type: "DELETE",
                success: function (data, textStatus, jqXHR) {
                    alert('Deleted!');
                }
            });
        });
    </script>
@endsection

@section('styles')
    <style>
        @media (max-width: 992px) {
            .actions-container {
                flex-direction: column !important;
                margin: 0 !important;
            }
            .actions-container > * {
                max-width: 100% !important;
                min-width: 100% !important;
                width: 100% !important;
                margin: 0 0 8px 0 !important;
            }
        }
    </style>
@endsection
