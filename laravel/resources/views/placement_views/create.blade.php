@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Placement Create</div>
                 <div class="panel-body">
                 @if(Session::has('flash_message'))
                    <div id="successMessage" class="alert alert-success">
                        <span class="glyphicon glyphicon-ok"></span>
                            <em> {!! session('flash_message') !!}</em>
                    </div>
                @endif
                    <form class="form-horizontal" role="form" action="{{ route('placement_views.store') }}" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="form-group{{ $errors->has('unit_id') ? ' has-error' : '' }}">
                            <label for="unit_id" class="col-md-4 control-label">Unit</label>
                            <div class="col-md-6">
                                <select class="form-control" name="unit_id" id="unit_id">
                                  @if (count($units) > 0)
                                      <option value="">Select From ...</option>
                                        @foreach($units as $unit)
                                            <option value="{{$unit->id}}" @if (old("unit_id") == $unit->id) selected="selected" @endif  @if ( $unit->id == $lid) selected="selected"
                                            @endif
                                            >{{$unit->title}}</option>
                                        @endforeach
                                 @endif
                                </select>

                                @if ($errors->has('unit_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('unit_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
                            <label for="question" class="col-md-4 control-label">
                                Question
                            </label>
                            <div class="col-md-6">
                                <textarea id="question" class="form-control"  name="question" placeholder="Enter Question"> {{ old('question') }}</textarea>

                                @if ($errors->has('question'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('question') }}</strong>
                                    </span>
                                @endif
                            </div>
                          </div>


                        <div class="form-group{{ $errors->has('order_no') ? ' has-error' : '' }}">
                            <label for="order" class="col-md-4 control-label">Order No</label>

                            <div class="col-md-6">
                                  <select class="form-control" name="order" id="order">
                                    <option value="1">1</option>
                                    @if ($total_placements > 0)
                                          @for($count = 2; $count <= $total_placements + 1; $count++)
                                            <option <?php echo ($count > $total_placements) ? 'selected="selected"' : ''; ?> value="{{$count}}">{{$count}}</option>
                                        @endfor
                                      @endif
                                    </select>

                                @if ($errors->has('order'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('order') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('dependency') ? ' has-error' : '' }}">
                            <label for="is_active" class="col-md-4 control-label">Is Active</label>
                            <div class="col-md-6 radio">
                                <label for="is_active" class="col-md-3"> <input checked="checked" type="checkbox" name="is_active" value="1"></label>
                                @if ($errors->has('is_active'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('is_active') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                               <a class="btn btn-default" href="{{ route('placement_views.index') }}">Back</a>
                                  <button class="btn btn-primary" type="submit" >
                                      Submit
                                  </button>
                             </div>
                        </div>
            </form>
            @if (count($placements) > 0)
            <div class="row">
                <div class="col.md.12">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="col-md">ID</th>
                                <th class="col-md">Order No</th>
                                <th class="col-md">Question</th>
                                <th class="col-md">Unit</th>
                                <th class="col-md">Active</th>
                                <th class="col-md-3">OPTIONS</th>
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
                                        <a class="btn btn-warning" href="{{ route('placement_views.edit', $placement->id) }}">Edit</a>
                                        <form action="{{ route('placement_views.destroy', $placement->id) }}"
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
            @endif

              </div>
            </div>
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
