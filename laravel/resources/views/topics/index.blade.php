@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Topics')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manage Topics</li>
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
            Manage Topics
            <a class="btn btn-dark btn-sm" href="{{ route('topics.create') }}">+ add topic</a>
        </div>
        <div class="card-body p-0">
            <form class="filters-container d-flex flex-row flex-wrap m-2" role="form" action="{{ route('topics.index') }}" method="GET">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="filter input-group mr-2 {{ $errors->has('level_id') ? ' has-error' : '' }}">
                    <div class="input-group-prepend">
                        <label for="level_id" class="input-group-text">Module</label>
                    </div>
                    <select class="form-control" name="level_id" id="level_id">
                        @if (count($levels) > 0)
                            <option value="">Select From ...</option>
                            @foreach($levels as $level)
                                <option <?php echo ($level_id == $level->id) ? 'selected="selected"' : ''; ?> value="{{$level->id}}"
                                >{{$level->title}}</option>
                            @endforeach
                        @endif
                    </select>
                    @if ($errors->has('level_id'))
                        <span class="form-text">
                            <strong>{{ $errors->first('level_id') }}</strong>
                        </span>
                    @endif
                </div>
                <div id="unit_options"> </div>
                <div class="filter input-group mr-2 {{ $errors->has('unit_id') ? ' has-error' : '' }}">
                    <div class="input-group-prepend">
                        <label for="unit_id" class="input-group-text">Unit</label>
                    </div>
                    <select class="form-control" name="unit_id" id="unit_id">
                        @if (count($units) > 0)
                            <option value="">Select From ...</option>
                            @foreach($units as $unit)
                                <option <?php echo ($unit_id == $unit->id) ? 'selected="selected"' : ''; ?> value="{{$unit->id}}"
                                >{{$unit->title}}</option>
                            @endforeach
                        @endif
                    </select>
                    @if ($errors->has('unit_id'))
                        <span class="form-text">
						    <strong>{{ $errors->first('unit_id') }}</strong>
						</span>
                    @endif
                </div>
                <button type="submit" class="filter-button btn btn-outline-secondary" style="max-width: 195px;">
                    Search
                </button>
            </form>
            <div class="d-flex justify-content-center mt-2">
                {{ $topics->links() }}
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th style="min-width: 120px;">
                            Image
                        </th>
                        <th style="min-width: 120px;">
                            ID
                            <a href="{{ route('topics.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 120px;">
                            Order No
                            <a href="{{ route('topics.index', array_merge(request()->all(), ['sort' => 'order_no', 'order' => ((request()->sort == 'order_no' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'order_no' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'order_no' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'order_no' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 120px;">
                            Title
                            <a href="{{ route('topics.index', array_merge(request()->all(), ['sort' => 'title', 'order' => ((request()->sort == 'title' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'title' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'title' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'title' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 140px;">
                            Short Name
                            <a href="{{ route('topics.index', array_merge(request()->all(), ['sort' => 'short_name', 'order' => ((request()->sort == 'short_name' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'short_name' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'short_name' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'short_name' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 160px;">
                            Dependency
                            <a href="{{ route('topics.index', array_merge(request()->all(), ['sort' => 'dependency', 'order' => ((request()->sort == 'dependency' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'dependency' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'dependency' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'dependency' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 180px;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr style="background: #999999;">
                        <td></td>
                        <td>
                            <input type="number" min="0" name="id" id="id-filter" style="width: 50px;">
                        </td>
                        <td>
                            <input type="number"  min="0" name="order_no" id="order-filter" style="width: 90px;">
                        </td>
                        <td>
                            <input type="text" name="title" id="title-filter" style="width: 100%;">
                        </td>
                        <td>
                            <input type="text" name="short_name" id="short-name-filter" style="width: 100%;">
                        </td>
                        <td style="width: 120px;"></td>
                        <td class="text-right">
                            <a href="javascript:void(0);" onclick="filter()" class="btn btn-dark">Filter</a>
                        </td>
                    </tr>
                    @foreach($topics as $topic)
                        <tr>
                            <td><img class="show-img" src="{{ URL::asset($topic->icon_src) }}" /></td>
                            <td>{{$topic->id}}</td>
                            <td>{{$topic->order_no}}</td>
                            <td>{{$topic->title}}</td>
                            <td>{{$topic->short_name}}</td>
                            <td>{{($topic->dependency == true) ? 'Yes' : 'No'}}</td>
                            <td class="text-right">
                            <!-- <a class="btn btn-dark" href="{{ route('topics.show', $topic->id) }}">View</a> -->
                                <a class="btn btn-dark" href="{{ route('topics.edit', $topic->id) }}">Edit</a>
                                <form action="{{ route('topics.destroy', $topic->id) }}"
                                      method="POST" style="display: inline;"
                                      onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="level_id" value="{{ $level_id }}">
                                    <input type="hidden" name="unit_id" value="{{ $unit_id }}">
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-2">
                {{ $topics->links() }}
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        setTimeout(function() {
            $('#successMessage').fadeOut('fast');
        }, 4000); // <-- time in milliseconds
    });
    function filter() {
        let url = new URL(window.location.href);
        const id = document.getElementById("id-filter").value;
        const order = document.getElementById("order-filter").value;
        const title = document.getElementById("title-filter").value;
        const shortName = document.getElementById("short-name-filter").value;
        if(id) {
            url.searchParams.set('id', id);
        } else if (url.searchParams.get('id')) {
            url.searchParams.delete('id');
        }
        if(order) {
            url.searchParams.set('order_no', order);
        } else if (url.searchParams.get('order_no')) {
            url.searchParams.delete('order_no');
        }
        if(title) {
            url.searchParams.set('title', title);
        } else if (url.searchParams.get('title')) {
            url.searchParams.delete('title');
        }
        if(shortName) {
            url.searchParams.set('short_name', shortName);
        } else if (url.searchParams.get('short_name')) {
            url.searchParams.delete('short_name');
        }
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }
    function init() {
        const url = new URL(window.location.href);

        document.getElementById("id-filter").value = url.searchParams.get('id');
        document.getElementById("order-filter").value = url.searchParams.get('order_no');
        document.getElementById("title-filter").value = url.searchParams.get('title');
        document.getElementById("short-name-filter").value = url.searchParams.get('short_name');

        document.querySelector('#level_id [value="' + url.searchParams.get('level_id') + '"]').selected = true;
        document.querySelector('#unit_id [value="' + url.searchParams.get('unit_id') + '"]').selected = true;
    }
    window.onload = init;
</script>
@endsection

@section('styles')
    <style>
        .input-group-text {
            min-width: 120px;
            white-space: initial;
            text-align: left;
        }

        .filter {
            width: 380px;
        }

        @media (max-width: 1280px) {
            .filters-container > div {
                flex-direction: column !important;
                margin: 0 !important;
            }

            .filters-container .filter > *:not(a) {
                max-width: 100% !important;
                min-width: 100% !important;
                width: 100% !important;
                margin-bottom: 8px;
            }
        }
    </style>
@endsection
