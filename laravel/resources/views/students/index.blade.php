@extends('layouts.app')

@section('title', 'Gizmo - Admin: Students')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manage Students</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Manage Students</div>
        <!-- PerPage Dropdown -->
        <div class="d-flex justify-content-between align-items-center mt-2 mx-3">
            {{ $students->links() }}
            <div class="form-inline">
                <label for="per_page" class="mr-2">Students Per Page:</label>
                <select class="form-control" id="per_page" onchange="perPage(this.value)">
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250</option>
                    <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500</option>
                    <option value="1000" {{ request('per_page') == 1000 ? 'selected' : '' }}>1000</option>
                </select>
            </div>
        </div>
        <!-- End PerPage Dropdown -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>
                            ID <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            First Name <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'first_name', 'order' => ((request()->sort == 'first_name' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'first_name' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'first_name' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'first_name' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Last Name <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'last_name', 'order' => ((request()->sort == 'last_name' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'last_name' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'last_name' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'last_name' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Email <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'email', 'order' => ((request()->sort == 'email' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'email' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'email' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'email' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Registration time <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'created_at', 'order' => ((request()->sort == 'created_at' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'created_at' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'created_at' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'created_at' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Last action time <a href="{{ route('students.index', array_merge(request()->all(), ['sort' => 'date', 'order' => ((request()->sort == 'date' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'date' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'date' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'date' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th>
                            Superuser
                        </th>
                        <th>
                            Teacher
                        </th>
                        <th>
                            Researcher
                        </th>
                        <th style="min-width: 170px;">Tags</th>
                        <th style="min-width: 160px;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr style="background: #999999;">
                        <td>
                            <input type="number" min="0" name="id" id="id-filter" style="width: 50px;">
                        </td>
                        <td>
                            <input type="text" name="first_name" id="first-name-filter" style="width: 110px;">
                        </td>
                        <td>
                            <input type="text" name="last_name" id="last-name-filter" style="width: 110px;">
                        </td>
                        <td>
                            <input type="email" name="email" id="email-filter" style="width: 140px;">
                        </td>
                        <td></td>
                        <td></td>
                        <td>
                            <select id="is-super-filter" name="is_super" class="form-control" style="min-width: 80px;">
                                <option value="">Any</option>
                                <option value="yes">Superuser</option>
                                <option value="no">Not Superuser</option>
                            </select>
                        </td>
                        <td>
                            <select id="is-teacher-filter" name="is_teacher" class="form-control" style="min-width: 80px;">
                                <option value="">Any</option>
                                <option value="yes">Teacher</option>
                                <option value="no">Not Teacher</option>
                            </select>
                        </td>
                        <td>
                            <select id="is-researcher-filter" name="is_researcher" class="form-control" style="min-width: 80px;">
                                <option value="">Any</option>
                                <option value="yes">Researcher</option>
                                <option value="no">Not Researcher</option>
                            </select>
                        </td>
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
                    @foreach($students as $student)
                        <tr>
                            <td>{{ $student->id }}</td>
                            <td style="max-width: 120px;">{{ $student->first_name }}</td>
                            <td style="max-width: 120px;">{{ $student->last_name }}</td>
                            <td style="max-width: 160px;">{{ $student->email }}</td>
                            <td style="max-width: 80px;">{{ $student->created_at? $student->created_at->format('Y/m/d H:i') : '' }}</td>
                            <td style="max-width: 80px;">{{ $student->date != null ? date('H:i d.m.Y', strtotime($student->date)) : 'Never' }}</td>
                            <td style="max-width: 40px;">{{ $student->is_super ? 'Yes' : 'No' }}</td>
                            <td style="max-width: 40px;">{{ $student->is_teacher ? 'Yes' : 'No' }}</td>
                            <td style="max-width: 40px;">{{ $student->is_researcher ? 'Yes' : 'No' }}</td>
                            <td style="max-width: 170px;">
                                @if($student->tags->count())
                                    @foreach($student->tags as $tag)
                                        <span class="badge badge-pill badge-light m-1" style="cursor:default;">{{ $tag->name }}</span>
                                    @endforeach
                                @else
                                    <span class="badge badge-pill badge-light m-1" style="cursor:default;">None</span>
                                @endif
                            </td>
                            <td class="flex flex-row justify-content-end mb-2" style="min-width: 230px">
                                <a href="{{ route('students.login', $student->id) }}" target="_blank" class="btn btn-outline-dark">Login</a>
                                <a href="{{ route('students.edit', $student->id) }}" class="btn btn-dark">Edit</a>
                                <form action="{{ route('students.delete', $student->id) }}"
                                      method="POST" style="display: inline;"
                                      onsubmit="if(confirm('Are you about to delete {{ $student->email }}, all participant information will be lost. This action is irreversible.')) { return true } else {return false };">
                                    <input type="hidden" name="_method" value="POST">
                                    {{ csrf_field() }}
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
    <!-- PerPage Dropdown Bottom of Page -->
    <!-- Probably a better way to do this... -->
    <div class="d-flex justify-content-between align-items-center mt-2 mx-3">
        {{ $students->links() }}
        <div class="form-inline">
            <label for="per_page_bottom" class="mr-2">Students Per Page:</label>
            <select class="form-control" id="per_page_bottom" onchange="perPage(this.value)">
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250</option>
                <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500</option>
                <option value="1000" {{ request('per_page') == 1000 ? 'selected' : '' }}>1000</option>
             </select>
        </div>
    </div>
    <!-- End PerPage Dropdown Bottom of Page-->
@endsection

@section('scripts')
    <script type="text/javascript">
        function filter() {
            let url = new URL(window.location.href);
            const id = document.getElementById("id-filter").value;
            const first_name = document.getElementById("first-name-filter").value;
            const last_name = document.getElementById("last-name-filter").value;
            const email = document.getElementById("email-filter").value;
            const is_super = document.getElementById("is-super-filter").value;
            const is_teacher = document.getElementById("is-teacher-filter").value;
            const is_researcher = document.getElementById("is-researcher-filter").value;

            // Set all filter parameters
            if (id) url.searchParams.set('id', id);
            else url.searchParams.delete('id');
            
            if (first_name) url.searchParams.set('first_name', first_name);
            else url.searchParams.delete('first_name');
            
            if (last_name) url.searchParams.set('last_name', last_name);
            else url.searchParams.delete('last_name');
            
            if (email) url.searchParams.set('email', email);
            else url.searchParams.delete('email');
            
            if (is_super) url.searchParams.set('is_super', is_super);
            else url.searchParams.delete('is_super');
            
            if (is_teacher) url.searchParams.set('is_teacher', is_teacher);
            else url.searchParams.delete('is_teacher');
            
            if (is_researcher) url.searchParams.set('is_researcher', is_researcher);
            else url.searchParams.delete('is_researcher');

            // Tag filter
            let tagIds = $('#tag_id_hidden').val();
            // Apply (set or delete) generic helper
            function applyParam(key, val) {
                if (val && val !== '') { url.searchParams.set(key, val); } else { url.searchParams.delete(key); }
            }
            applyParam('id', id);
            applyParam('first_name', first_name);
            applyParam('last_name', last_name);
            applyParam('email', email);
            applyParam('is_super', is_super);
            applyParam('is_teacher', is_teacher);
            applyParam('is_researcher', is_researcher);
            if (tagIds) {
                url.searchParams.set('tag_id', tagIds);
            } else if (url.searchParams.get('tag_id')) {
                url.searchParams.delete('tag_id');
            }
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        $(document).ready(function () {
            // Tag filter badge click handler (copied from units page)
            $(document).on('click', '.tag-filter-badge', function() {
                let tagId = $(this).data('tag-id').toString();
                let selected = $('#tag_id_hidden').val().split(',').filter(Boolean);
                if ($(this).hasClass('badge-primary')) {
                    // Deselect
                    selected = selected.filter(id => id !== tagId);
                    $(this).removeClass('badge-primary').addClass('badge-light');
                } else {
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
        });

        // PerPage Dropdown Handler
        function perPage(value) {
            let url = new URL(window.location.href); // make url obj from current page
            url.searchParams.set('per_page', value); // set per_page query to selected value
            url.searchParams.delete('page'); // reset current page number to 1
            window.location.href = url.toString(); // redirects to new url
        }

        function init() {
            const url = new URL(window.location.href);
            document.getElementById("id-filter").value = url.searchParams.get('id') || '';
            document.getElementById("first-name-filter").value = url.searchParams.get('first_name') || '';
            document.getElementById("last-name-filter").value = url.searchParams.get('last_name') || '';
            document.getElementById("email-filter").value = url.searchParams.get('email') || '';
            document.getElementById("is-super-filter").value = url.searchParams.get('is_super') || '';
            document.getElementById("is-teacher-filter").value = url.searchParams.get('is_teacher') || '';
            document.getElementById("is-researcher-filter").value = url.searchParams.get('is_researcher') || '';
            
            // Initialize tag filter badges
            const tagIds = url.searchParams.get('tag_id');
            if (tagIds) {
                const selectedTags = tagIds.split(',');
                // Reset all badges to light first
                $('.tag-filter-badge').removeClass('badge-primary').addClass('badge-light');
                // Then set the selected ones to primary
                selectedTags.forEach(tagId => {
                    $(`.tag-filter-badge[data-tag-id='${tagId}']`).removeClass('badge-light').addClass('badge-primary');
                });
                // Update hidden input
                $('#tag_id_hidden').val(tagIds);
            }
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
    </style>
@endsection
