@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Custom Search</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <form class="form-horizontal" role="form" action="{{ route('question_views.index') }}" method="GET">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="col-md-4 form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                                <label for="level_id" class="col-md-4 control-label">Level</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="level_id" id="level_id">

                                        @if (count($levels) > 0)
                                            <option value="">Select From ...</option>
                                            @foreach($levels as $level)
                                                <option value="{{$level->id}}" <?php echo ($level->id == $level_id) ? 'selected' : ''; ?>
                                                >{{$level->title}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 form-group{{ $errors->has('unit_id') ? ' has-error' : '' }}">
                                <label for="unit_id" class="col-md-4 control-label">Unit</label>

                                <div class="col-md-8">
                                    <select class="form-control" name="unit_id" id="unit_id">
                                        <option value="">Select From ...</option>
                                        @if (count($units) > 0)

                                            @foreach($units as $unit)
                                                <option value="{{$unit->id}}" <?php echo ($unit->id == $unit_id) ? 'selected' : ''; ?>
                                                >{{$unit->title}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 form-group{{ $errors->has('topic_id') ? ' has-error' : '' }}">
                                <label for="topic_id" class="col-md-4 control-label">Topic</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="topic_id" id="topic_id">
                                        <option value="">Select From ...</option>
                                        @if (count($topics) > 0)
                                            @foreach($topics as $topic)
                                                <option value="{{$topic->id}}" <?php echo ($topic->id == $topic_id) ? 'selected' : ''; ?>
                                                >{{$topic->title}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 form-group{{ $errors->has('lesson_id') ? ' has-error' : '' }}">
                                <label for="lesson_id" class="col-md-4 control-label">Lesson</label>

                                <div class="col-md-8">
                                    <select class="form-control" name="lesson_id" id="lesson_id">
                                        <option value="">Select From ...</option>
                                        @if (count($lessons) > 0)
                                            @foreach($lessons as $lesson)
                                                <option value="{{$lesson->id}}" <?php echo ($lesson->id == $lesson_id) ? 'selected' : ''; ?>
                                                >{{$lesson->title}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="lesson_id" class="col-md-4 control-label">Question</label>
                                <div class="col-md-8">
                                    <input type="text" name="question" class="form-control" value="{{ request()->question }}">
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="lesson_id" class="col-md-4 control-label">Type</label>
                                <div class="col-md-8">
                                    <input type="text" name="type" class="form-control" value="{{ request()->type }}">
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="lesson_id" class="col-md-4 control-label">Reply mode</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="reply_mode">
                                        <option value="">Select From ...</option>
                                        @if (count($reply_modes) > 0)
                                            @foreach($reply_modes as $reply_mode)
                                                <option value="{{$reply_mode->code}}" <?php echo ($reply_mode->code == request()->reply_mode) ? 'selected' : ''; ?>
                                                >{{$reply_mode->mode}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button class="btn btn-primary" type="submit" >Search</button>
                                </div>
                            </div>
                            <input type="hidden" name="sort" value="{{ request()->sort }}">
                            <input type="hidden" name="order" value="{{ request()->order }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Question / Show </div>

            <div class="panel-body">

                <div class="row">
                    <div class="col-md-12">
                        {{ $questions->links() }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="col-md">Actions</th>
                                    <th class="col-md">
                                        Level <a href="{{ route('question_views.index', array_merge(request()->all(), ['sort' => 'level_id', 'order' => ((request()->sort == 'level_id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'level_id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'level_id' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'level_id' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                        </a></th>
                                    <th class="col-md">
                                        Unit <a href="{{ route('question_views.index', array_merge(request()->all(), ['sort' => 'unit_id', 'order' => ((request()->sort == 'unit_id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'unit_id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'unit_id' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'unit_id' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                        </a></th>
                                    <th class="col-md">
                                        Topic <a href="{{ route('question_views.index', array_merge(request()->all(), ['sort' => 'topic_id', 'order' => ((request()->sort == 'topic_id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'topic_id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'topic_id' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'topic_id' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                        </a></th>
                                    <th class="col-md">
                                        Lesson <a href="{{ route('question_views.index', array_merge(request()->all(), ['sort' => 'lesson_order', 'order' => ((request()->sort == 'lesson_order' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'lesson_order' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'lesson_order' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'lesson_order' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                        </a></th>
                                    <th class="col-md">
                                        Question <a href="{{ route('question_views.index', array_merge(request()->all(), ['sort' => 'question', 'order' => ((request()->sort == 'question' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'question' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'question' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'question' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                        </a></th>
                                    <th class="col-md">
                                        ReplyMode <a href="{{ route('question_views.index', array_merge(request()->all(), ['sort' => 'reply_mode', 'order' => ((request()->sort == 'reply_mode' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'reply_mode' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                            <i class="fa fa-fw fa-sort{{ (request()->sort == 'reply_mode' && request()->order == 'asc') ? '-asc' : '' }}{{ (request()->sort == 'reply_mode' && request()->order == 'desc') ? '-desc' : '' }}"></i>
                                        </a></th>
                                </tr>
                                </thead>


                                <tbody>
                                @foreach($questions as $question)
                                    <tr style="height:40px; overflow:hidden">
                                        <td class="text-right" style="width:200px">

                                            <a class="btn btn-primary" href="{{ route('question_views.show', $question->id) }}">View</a>
                                            <a class="btn btn-warning" href="{{ route('question_views.edit', $question->id) }}">Edit</a>
                                            <form action="{{ route('question_views.destroy', $question->id) }}"
                                                  method="POST" style="display: inline;"
                                                  onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button class="btn btn-danger" type="submit">Delete</button>
                                            </form>
                                        </td>
                                        <td>{{$question->ltitle}}</td>
                                        <td>{{$question->utitle}}</td>
                                        <td>{{$question->ttitle}}</td>
                                        <td>{{$question->title}}</td>
                                        <td>{{strip_tags ($question->question)}}</td>
                                        <td>{{(isset($qrmodes[$question->reply_mode]) ? $qrmodes[$question->reply_mode] : 'Unknown')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {{ $questions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ URL::asset('js/jquery.jslatex.packed.js') }}"></script>
    <script>
        $(document).ready(function(){
            $('td span').css("display", "inline-block");
            $('.latex').latex();
        });
    </script>
@endsection
