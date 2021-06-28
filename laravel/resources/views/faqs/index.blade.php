@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Faqs')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manage Faqs</li>
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
            Manage FAQs
            <a class="btn btn-dark btn-sm" href="{{ route('faqs.create') }}">+ add FAQ</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th style="min-width: 60px;">
                            ID
                            <a href="{{ route('faqs.index', array_merge(request()->all(), ['sort' => 'id', 'order' => ((request()->sort == 'id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 120px;">
                            Order No
                            <a href="{{ route('faqs.index', array_merge(request()->all(), ['sort' => 'order_no', 'order' => ((request()->sort == 'order_no' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'order_no' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'order_no' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'order_no' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th style="min-width: 180px;">
                            Title
                            <a href="{{ route('faqs.index', array_merge(request()->all(), ['sort' => 'title', 'order' => ((request()->sort == 'title' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'title' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'title' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'title' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a>
                        </th>
                        <th></th>
                        <th style="min-width: 160px;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr style="background: #999999;">
                        <td>
                            <input type="number" min="0" name="id" id="id-filter" style="width: 50px;">
                        </td>
                        <td>
                            <input type="number"  min="0" name="order_no" id="order-filter" style="width: 60px;">
                        </td>
                        <td>
                            <input type="text" name="title" id="title-filter" style="width: 100%;">
                        </td>
                        <td>
                            <select name="for" id="for-filter" style="width: 100%;">
                                <option value=""></option>
                                <option value="student">Student</option>
                                <option value="teacher">Teacher</option>
                            </select>
                        </td>
                        <td class="text-right">
                            <a href="javascript:void(0);" onclick="filter()" class="btn btn-dark">Filter</a>
                        </td>
                    </tr>

                    @foreach($faqs as $faq)
                        <tr>
                            <td>{{$faq->id}}</td>
                            <td>{{$faq->order_no}}</td>
                            <td>{{$faq->title}}</td>
                            <td>
                                @if($faq->is_for_student)
                                    <badge class="badge badge-primary">Student</badge>
                                @endif
                                @if($faq->is_for_teacher)
                                    <badge class="badge badge-dark">Teacher</badge>
                                @endif
                            </td>
                            <td class="text-right">
                                <a class="btn btn-dark" href="{{ route('faqs.edit', $faq->id) }}">Edit</a>
                                <form action="{{ route('faqs.destroy', $faq->id) }}"
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
            const forFilter = document.getElementById("for-filter").value;
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
            if(forFilter) {
                url.searchParams.set('for', forFilter);
            } else if (url.searchParams.get('for')) {
                url.searchParams.delete('for');
            }
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        function initFilters() {
            const url = new URL(window.location.href);
            document.getElementById("id-filter").value = url.searchParams.get('id');
            document.getElementById("order-filter").value = url.searchParams.get('order_no');
            document.getElementById("title-filter").value = url.searchParams.get('title');
            document.getElementById("for-filter").value = url.searchParams.get('for');
        }
        window.onload = initFilters;
    </script>
@endsection
