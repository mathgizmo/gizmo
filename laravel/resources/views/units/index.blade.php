@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Units')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manage Units</li>
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
            Manage Units
            <a class="btn btn-dark btn-sm" href="{{ route('units.create') }}">+ add unit</a>
        </div>
        <div class="card-body p-0">
            <form class="filters-container d-flex flex-row flex-wrap m-2" role="form"
                  action="{{ route('units.index') }}" method="GET">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="filter input-group mr-2 {{ $errors->has('level_id') ? ' has-error' : '' }}">
                    <div class="input-group-prepend">
                        <label for="level_id" class="input-group-text">Module</label>
                    </div>
                    <select class="form-control" name="level_id" id="level_id">
                        @if (count($levels) > 0)
                            <option value="">Select From ...</option>
                            @foreach($levels as $level)
                                <option
                                    <?php echo ($level_id == $level->id) ? 'selected="selected"' : ''; ?> value="{{$level->id}}"
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
                <button type="submit" class="filter-button btn btn-outline-secondary" style="max-width: 195px;">
                    Search
                </button>
            </form>
            <div class="d-flex justify-content-center mt-2">
                {{ $units->links() }}
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th style="min-width: 120px;">
                            Unit ID
                            <a href="{{ route('units.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 120px;">
                            Order No
                            <a href="{{ route('units.index', array_merge(request()->all(), ['sort' => 'order_no', 'order' => ((request()->sort == 'order_no' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'order_no' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'order_no' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'order_no' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 160px;">
                            Unit Title
                            <a href="{{ route('units.index', array_merge(request()->all(), ['sort' => 'title', 'order' => ((request()->sort == 'title' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'title' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'title' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'title' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 150px;">
                            Dependency
                            <a href="{{ route('units.index', array_merge(request()->all(), ['sort' => 'dependency', 'order' => ((request()->sort == 'dependency' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'dependency' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'dependency' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'dependency' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 170px;">Tags</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr style="background: #999999;">
                        <td>
                            <input type="number" min="0" name="id" id="id-filter" style="width: 50px;">
                        </td>
                        <td>
                            <input type="number" min="0" name="order_no" id="order-filter" style="width: 60px;">
                        </td>
                        <td>
                            <input type="text" name="title" id="title-filter" style="width: 100%;">
                        </td>
                        <td></td>
                        <td>
                            <div id="tag-filter-group" class="d-flex flex-wrap">
                                <span class="badge badge-pill tag-filter-badge {{ in_array('none', $selected_tag_ids ?? []) ? 'badge-primary' : 'badge-light' }} m-1" data-tag-id="none" style="cursor:pointer;user-select:none;">None</span>
                                @foreach($tags as $tag)
                                    <span class="badge badge-pill tag-filter-badge {{ in_array($tag->id, $selected_tag_ids ?? []) ? 'badge-primary' : 'badge-light' }} m-1"
                                          data-tag-id="{{ $tag->id }}" style="cursor:pointer;user-select:none;">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                            <input type="hidden" name="tag_id" id="tag_id_hidden" value="{{ implode(',', $selected_tag_ids ?? []) }}">
                        </td>
                        <td class="text-right">
                            <a href="javascript:void(0);" onclick="filter()" class="btn btn-dark">Filter</a>
                        </td>
                    </tr>
                    @foreach($units as $unit)
                        <tr>
                            <td>{{$unit->id}}</td>
                            <td>{{$unit->order_no}}</td>
                            <td>{{$unit->title}}</td>
                            <td>{{($unit->dependency == true) ? 'Yes' : 'No'}}</td>
                            <td>
                                @foreach($unit->tags as $tag)
                                    <span class="badge badge-info">{{ $tag->name }}</span>
                                @endforeach
                            </td>
                            <td class="text-right">
                            <!-- <a class="btn btn-dark" href="{{ route('units.show', $unit->id) }}">View</a> -->
                                <a class="btn btn-dark" href="{{ route('units.edit', $unit->id) }}">Edit</a>
                                <form action="{{ route('units.destroy', $unit->id) }}"
                                      method="POST" style="display: inline;"
                                      onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="level_id" value="{{ $level_id }}">
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-2">
                {{ $units->links() }}
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () {
                $('#successMessage').fadeOut('fast');
            }, 4000);

            // Tag filter badge click handler
            $(document).on('click', '.tag-filter-badge', function() {
                let tagId = $(this).data('tag-id').toString();
                let selected = $('#tag_id_hidden').val().split(',').filter(Boolean);
                if ($(this).hasClass('badge-primary')) {
                    // Deselect
                    selected = selected.filter(id => id !== tagId);
                    $(this).removeClass('badge-primary').addClass('badge-light');
                } else {
                    // Select
                    if(tagId === 'none') {
                        // If 'none' is selected, deselect all others
                        selected = ['none'];
                        $('.tag-filter-badge').removeClass('badge-primary').addClass('badge-light');
                        $(this).removeClass('badge-light').addClass('badge-primary');
                    } else {
                        // If any tag is selected, remove 'none' if present
                        selected = selected.filter(id => id !== 'none');
                        selected.push(tagId);
                        $(this).removeClass('badge-light').addClass('badge-primary');
                        // Deselect 'none' badge
                        $(".tag-filter-badge[data-tag-id='none']").removeClass('badge-primary').addClass('badge-light');
                    }
                }
                // Remove duplicates
                selected = [...new Set(selected)];
                $('#tag_id_hidden').val(selected.join(','));
            });

            // On form submit, convert tag_id_hidden to hidden inputs
            $('form[role="form"]').on('submit', function(e) {
                let selected = $('#tag_id_hidden').val().split(',').filter(Boolean);
                // Remove any previous tag_id[]
                $(this).find('input[name="tag_id[]"]').remove();
                selected.forEach(function(id) {
                    $('<input>').attr({type: 'hidden', name: 'tag_id[]', value: id}).appendTo('form[role="form"]');
                });
            });
        });

        function filter() {
            let url = new URL(window.location.href);
            const id = document.getElementById("id-filter").value;
            const order = document.getElementById("order-filter").value;
            const title = document.getElementById("title-filter").value;
            if (id) {
                url.searchParams.set('id', id);
            } else if (url.searchParams.get('id')) {
                url.searchParams.delete('id');
            }
            if (order) {
                url.searchParams.set('order_no', order);
            } else if (url.searchParams.get('order_no')) {
                url.searchParams.delete('order_no');
            }
            if (title) {
                url.searchParams.set('title', title);
            } else if (url.searchParams.get('title')) {
                url.searchParams.delete('title');
            }
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }

        function init() {
            const url = new URL(window.location.href);
            document.getElementById("id-filter").value = url.searchParams.get('id');
            document.getElementById("order-filter").value = url.searchParams.get('order_no');
            document.getElementById("title-filter").value = url.searchParams.get('title');
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

        .tag-filter-badge {
            border: 1px solid #007bff;
            color: #007bff;
            background: #fff;
            transition: all 0.2s;
        }
        .tag-filter-badge.badge-primary {
            background: #007bff;
            color: #fff;
        }
        .tag-filter-badge.badge-light {
            background: #fff;
            color: #007bff;
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
