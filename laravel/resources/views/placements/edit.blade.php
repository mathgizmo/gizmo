@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Placements')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('placements.index')  }}">Manage Placements</a></li>
    <li class="breadcrumb-item active">Edit Placement</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Edit Placement</div>
        <form role="form" action="{{ route('placements.update', $placement->id) }}" method="POST">
            <div class="card-body p-0">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group row mt-3 {{ $errors->has('unit_id') ? ' has-error' : '' }}">
                    <label for="unit_id"
                           class="col-md-2 form-control-label ml-3 font-weight-bold">Unit</label>
                    <div class="col-md-8">
                        <select class="form-control" name="unit_id" id="unit_id">
                            @if (count($units) > 0)
                                <option value="">Select From ...</option>
                                @foreach($units as $unit)
                                    <option value="{{$unit->id}}"
                                            @if (old("unit_id") == $unit->id) selected="selected" @endif
                                            @if ($unit->id == $placement->unit_id) selected="selected"
                                            @endif
                                    >{{$unit->title}}
                                        @endforeach
                                    </option>
                                    @endif
                        </select>
                        @if ($errors->has('unit_id'))
                            <span class="form-text">
                                            <strong>{{ $errors->first('unit_id') }}</strong>
                                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('question') ? ' has-error' : '' }}">
                    <label for="question"
                           class="col-md-2 form-control-label ml-3 font-weight-bold">Question</label>
                    <div class="col-md-8">
                                    <textarea id="question" class="form-control"
                                              name="question">{{$placement->question}}</textarea>
                        @if ($errors->has('question'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('question') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row{{ $errors->has('is_active') ? ' has-error' : '' }}">
                    <label for="type" class="col-md-2 form-control-label ml-3 font-weight-bold">
                        Is Active
                    </label>

                    <div class="col-md-8 radio">
                        <label for="type" class="col-md-3"> <input
                                    {{ ($placement->is_active == 1) ? 'checked="checked"' : ''}} type="checkbox"
                                    name="is_active" value="1"></label>
                        @if ($errors->has('is_active'))
                            <span class="form-text">
                                                <strong>{{ $errors->first('is_active') }}</strong>
                                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('order') ? ' has-error' : '' }}">
                    <label for="order" class="col-md-2 form-control-label ml-3 font-weight-bold">Order
                        No</label>
                    <div class="col-md-8">
                        <select class="form-control" name="order" id="order">
                            <option value="1">1</option>
                            @if ($total_placements > 0)
                                @for($count = 2; $count <= $total_placements + 1; $count++)
                                    <option
                                        <?php echo ($count == $placement->order) ? 'selected="selected"' : ''; ?> value="{{$count}}">{{$count}}</option>
                                @endfor
                            @endif
                        </select>
                        @if ($errors->has('order'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('order') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a class="btn btn-secondary" href="{{ route('placements.index') }}">Back</a>
                <button class="btn btn-dark" type="submit">Update</button>
            </div>
        </form>
    </div>
@endsection

@section('styles')
    <style>
        @media screen and (max-width: 600px) {
            .col-md-8 {
                margin: 0 16px;
            }
        }
    </style>
@endsection
