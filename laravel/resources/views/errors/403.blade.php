@extends('layouts.auth')

@section('title', 'Gizmo - Admin: Forbidden')

@section('content')
    <div id="layoutError">
        <div id="layoutError_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <div class="text-center mt-4">
                                <h1 class="display-1">403</h1>
                                <p class="lead">Forbidden</p>
                                <p>Access to this resource is denied.</p>
                                <a href="{{url('/home')}}"><i class="fas fa-arrow-left mr-1"></i>Return to Dashboard</a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        body {
            background-color: #FFFCF5 !important;
        }
        .logo-img {
            filter: grayscale(50%) !important;
            -webkit-filter: grayscale(50%) !important;
        }
    </style>
@endsection
