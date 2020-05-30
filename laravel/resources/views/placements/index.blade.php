@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Placements')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manage Placements</li>
@endsection

@section('content')
    @if(Session::has('message'))
        <div id="successMessage" class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>
            <em> {!! session('message') !!}</em>
        </div>
    @endif
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row justify-content-between">
            Manage Placements
            <a class="btn btn-dark btn-sm" href="{{ route('placements.create') }}">+ add placement</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th style="min-width: 80px;">
                            ID
                            <a href="{{ route('placements.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 120px;">
                            Order No
                            <a href="{{ route('placements.index', array_merge(request()->all(), ['sort' => 'order', 'order' => ((request()->sort == 'order' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'order' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'order' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'order' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 160px;">
                            Question
                            <a href="{{ route('placements.index', array_merge(request()->all(), ['sort' => 'question', 'order' => ((request()->sort == 'question' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'question' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'question' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'question' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 120px;">
                            Unit
                            </a>
                        </th>
                        <th style="min-width: 120px;">
                            Active
                            <a href="{{ route('placements.index', array_merge(request()->all(), ['sort' => 'is_active', 'order' => ((request()->sort == 'is_active' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'is_active' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'is_active' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'is_active' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 180px;"></th>
                    </tr>
                    </thead>


                    <tbody>
                    @foreach($placements as $placement)
                        <tr>
                            <td>{{$placement->id}}</td>
                            <td>{{$placement->order}}</td>
                            <td>{{$placement->question}}</td>
                            <td>{{$placement->unit->title}}</td>
                            <td>{{($placement->is_active == 1) ? 'Yes' : 'No'}}</td>
                            <td class="text-right">
                                <a class="btn btn-dark" href="{{ route('placements.edit', $placement->id) }}">Edit</a>
                                <form action="{{ route('placements.destroy', $placement->id) }}"
                                      method="POST" style="display: inline;"
                                      onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection

@section('scripts')

<script>
    $(document).ready(function(){
        setTimeout(function() {
            $('#successMessage').fadeOut('fast');
}, 4000); // <-- time in milliseconds
    });
</script>

@endsection
