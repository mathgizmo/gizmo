@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Questions')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manage Questions</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row justify-content-between">
            Manage Questions
            <a class="btn btn-dark btn-sm" href="{{ route('questions.create') }}">+ add question</a>
        </div>
        <div class="card-body p-0">
            <form class="filters-container d-flex flex-column flex-wrap m-2" role="form" action="{{ route('questions.index') }}" method="GET">
                <div class="d-flex justify-content-start flex-row flex-wrap mb-2">
                    <div class="filter input-group mr-2 {{ $errors->has('level_id') ? ' has-error' : '' }}">
                        <div class="input-group-prepend">
                            <label for="level_id" class="input-group-text">Module</label>
                        </div>
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
                    <div class="filter input-group mr-2 {{ $errors->has('unit_id') ? ' has-error' : '' }}">
                        <div class="input-group-prepend">
                            <label for="unit_id" class="input-group-text">Unit</label>
                        </div>
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
                    <div class="filter input-group {{ $errors->has('topic_id') ? ' has-error' : '' }}">
                        <div class="input-group-prepend">
                            <label for="topic_id" class="input-group-text">Topic</label>
                        </div>
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
                <div class="d-flex justify-content-start flex-row mb-2">
                    <div class="filter input-group mr-2 {{ $errors->has('lesson_id') ? ' has-error' : '' }}">
                        <div class="input-group-prepend">
                            <label for="lesson_id" class="input-group-text">Lesson</label>
                        </div>
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
                    <div class="filter input-group mr-2">
                        <div class="input-group-prepend">
                            <label for="question_id" class="input-group-text">Question</label>
                        </div>
                        <input type="text" name="question" class="form-control" value="{{ request()->question }}">
                    </div>
                    <div class="filter input-group">
                        <div class="input-group-prepend">
                            <label for="type_id" class="input-group-text">Type</label>
                        </div>
                        <input type="text" name="type" class="form-control" value="{{ request()->type }}">
                    </div>
                </div>
                <div class="d-flex justify-content-start flex-row mb-2">
                    <div class="filter input-group mr-2">
                        <div class="input-group-prepend">
                            <label for="reply_mode" class="input-group-text">Reply mode</label>
                        </div>
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
                    <button type="submit" class="filter-button btn btn-outline-secondary" style="max-width: 195px;">Search</button>
                </div>
                <input type="hidden" name="sort" value="{{ request()->sort }}">
                <input type="hidden" name="order" value="{{ request()->order }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>
            <div class="d-flex justify-content-center mt-2">
                {{ $questions->links() }}
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>
                            Level <a href="{{ route('questions.index', array_merge(request()->all(), ['sort' => 'level_id', 'order' => ((request()->sort == 'level_id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'level_id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'level_id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'level_id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a></th>
                        <th>
                            Unit <a href="{{ route('questions.index', array_merge(request()->all(), ['sort' => 'unit_id', 'order' => ((request()->sort == 'unit_id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'unit_id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'unit_id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'unit_id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a></th>
                        <th>
                            Topic <a href="{{ route('questions.index', array_merge(request()->all(), ['sort' => 'topic_id', 'order' => ((request()->sort == 'topic_id' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'topic_id' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'topic_id' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'topic_id' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a></th>
                        <th>
                            Lesson <a href="{{ route('questions.index', array_merge(request()->all(), ['sort' => 'lesson_order', 'order' => ((request()->sort == 'lesson_order' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'lesson_order' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'lesson_order' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'lesson_order' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a></th>
                        <th>
                            Question <a href="{{ route('questions.index', array_merge(request()->all(), ['sort' => 'question', 'order' => ((request()->sort == 'question' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'question' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'question' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'question' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a></th>
                        <th>
                            ReplyMode <a href="{{ route('questions.index', array_merge(request()->all(), ['sort' => 'reply_mode', 'order' => ((request()->sort == 'reply_mode' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'reply_mode' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'reply_mode' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'reply_mode' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a></th>
                        <th>
                            Explanation <a href="{{ route('questions.index', array_merge(request()->all(), ['sort' => 'explanation', 'order' => ((request()->sort == 'explanation' && request()->order == 'desc') ? 'asc' : ((request()->sort == 'explanation' && request()->order == 'asc') ? '' : 'desc'))])) }}">
                                <i class="fa fa-fw fa-sort{{ (request()->sort == 'explanation' && request()->order == 'asc') ? '-up' : '' }}{{ (request()->sort == 'explanation' && request()->order == 'desc') ? '-down' : '' }}"></i>
                            </a></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($questions as $question)
                        <tr style="height:40px; overflow:hidden">
                            <td style="min-width: 90px;">{{$question->ltitle}}</td>
                            <td style="min-width: 90px;">{{$question->utitle}}</td>
                            <td style="min-width: 90px;">{{$question->ttitle}}</td>
                            <td style="min-width: 90px;">{{$question->title}}</td>
                            <td style="min-width: 90px;">{{strip_tags ($question->question)}}</td>
                            <td style="min-width: 90px;">{{(isset($qrmodes[$question->reply_mode]) ? $qrmodes[$question->reply_mode] : 'Unknown')}}</td>
                            <td style="min-width: 140px;" title="{{$question->explanation}}">{{trim($question->explanation) ? substr(trim($question->explanation), 0, 20).'...': ''}}</td>
                            <td class="text-right" style="min-width:220px">
                                <a class="btn btn-outline-dark" href="{{ route('questions.show', $question->id) }}">View</a>
                                <a class="btn btn-dark" href="{{ route('questions.edit', $question->id) }}">Edit</a>
                                @if(auth()->user()->isAdmin())
                                    <form action="{{ route('questions.destroy', $question->id) }}"
                                          method="POST" style="display: inline;"
                                          onsubmit="if(confirm('Delete? Are you sure?')) { return true } else {return false };">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button class="btn btn-danger" type="submit">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-2">
                {{ $questions->links() }}
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
        @media (max-width: 1366px) {
            .filters-container > div {
                flex-direction: column !important;
                margin: 0 !important;
            }
            .filters-container > div > *:not(a) {
                max-width: 100% !important;
                min-width: 100% !important;
                width: 100% !important;
                margin-bottom: 8px;
            }
        }
    </style>
@endsection
