@extends('layouts.app')

@section('title', 'Gizmo - Admin: Mails')

@section('breadcrumb')
    <li class="breadcrumb-item active">Mails</li>
@endsection

@section('content')
    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row justify-content-between">
            Mails
            <div class="pull-right">
                <a href="{{ route('mails.new') }}" class="btn btn-info btn-sm" style="margin-top: -5px;">+ new mail</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col">Subject</th>
                                <th scope="col">Type</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($mails as $mail)
                                <tr>
                                    <td>{{ $mail->subject }}</td>
                                    <td>{{ $mail->mail_type }}</td>
                                    <td class="d-flex flex-wrap flex-row justify-content-end">
                                        <a href="{{ route('mails.edit', $mail) }}" class="btn btn-info">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
