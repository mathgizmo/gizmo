@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Questions')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('questions.index')  }}">Manage Questions</a></li>
    <li class="breadcrumb-item active">Create Question</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Create Question</div>
        <form role="form" action="{{ route('questions.store') }}" method="POST" id="create-form">
        <div class="card-body p-0">
            @if(Session::has('flash_message'))
                <div id="successMessage" class="alert alert-success">
                    <span class="glyphicon glyphicon-ok"></span>
                    <em> {!! session('flash_message') !!}</em>
                </div>
            @endif
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group row mt-3 {{ $errors->has('level_id') ? ' has-error' : '' }}">
                    <label for="level_id" class="col-md-2 form-control-label ml-3 font-weight-bold">Module</label>

                    <div class="col-md-8">
                        <select class="form-control" name="level_id" id="level_id">

                            @if (count($levels) > 0)
                                <option value="">Select From ...</option>
                                @foreach($levels as $level)
                                    <option value="{{$level->id}}" @if (old("level_id") == $level->id) selected="selected" @endif  @if ($level->id == $lid) selected="selected"
                                            @endif
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
                </div>

                <div id="unit_options"> </div>
                <div class="form-group row{{ $errors->has('unit_id') ? ' has-error' : '' }}">
                    <label for="unit_id" class="col-md-2 form-control-label ml-3 font-weight-bold">Unit</label>

                    <div class="col-md-8">
                        <select class="form-control" name="unit_id" id="unit_id">
                            @if (count($units) > 0)
                                <option value="">Select From ...</option>
                                @foreach($units as $unit)
                                    <option value="{{$unit->id}}" @if (old("unit_id") == $unit->id) selected="selected" @endif  @if ($unit->id == $uid) selected="selected"
                                            @endif
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
                </div>

                <div class="form-group row{{ $errors->has('topic_id') ? ' has-error' : '' }}">
                    <label for="topic_id" class="col-md-2 form-control-label ml-3 font-weight-bold">Topic</label>

                    <div class="col-md-8">
                        <select class="form-control" name="topic_id" id="topic_id">

                            @if (count($topics) > 0)
                                <option value="">Select From ...</option>
                                @foreach($topics as $topic)
                                    <option value="{{$topic->id}}" @if (old("topic_id") == $topic->id) selected="selected" @endif  @if ($topic->id == $tid) selected="selected"
                                            @endif
                                    >{{$topic->title}}</option>
                                @endforeach
                            @endif

                        </select>

                        @if ($errors->has('topic_id'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('topic_id') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('lesson_id') ? ' has-error' : '' }}">
                    <label for="lesson_id" class="col-md-2 form-control-label ml-3 font-weight-bold">Lesson</label>

                    <div class="col-md-8">
                        <select class="form-control" name="lesson_id" id="lesson_id">

                            @if (count($lessons) > 0)
                                <option value="">Select From ...</option>
                                @foreach($lessons as $lesson)
                                    <option value="{{$lesson->id}}" @if (old("lesson_id") == $lesson->id) selected="selected" @endif  @if ($lesson->id == $lsnid) selected="selected"
                                            @endif
                                    >{{$lesson->title}}</option>
                                @endforeach
                            @endif
                        </select>

                        @if ($errors->has('lesson_id'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('lesson_id') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('reply_mode') ? ' has-error' : '' }}">
                    <label for="reply_mode" class="col-md-2 form-control-label ml-3 font-weight-bold">REPLY MODE</label>
                    <div class="col-md-8">
                        <select class="form-control" name="reply_mode" id="reply_mode">
                            @foreach($qrmodes as $qrmode)
                                @if (old('reply_mode') == $qrmode->code)
                                    <option value="{{$qrmode->code}}" selected>{{$qrmode->mode}}</option>
                                @else
                                    <option value="{{$qrmode->code}}">{{$qrmode->mode}}</option>
                                @endif

                            @endforeach
                        </select>

                        @if ($errors->has('reply_mode'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('reply_mode') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('question') ? ' has-error' : '' }}">
                    <label for="question" class="col-md-2 form-control-label ml-3 font-weight-bold">Question</label>
                    <div class="col-md-8">
                        <span>For LaTeX please use next format \($$latext here$$ \) or \(\(latext here\)\)</span>
                        <br />
                        <span>Use __ as placeholder in Fill in the blank questions</span>
                        <textarea id="question" class="form-control"  name="question" placeholder="Enter question text.."> {{ old('question') }}</textarea>

                        @if ($errors->has('question'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('question') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div id='question_order' style='display: none;'
                     class="form-group row{{ $errors->has('question_order') ? ' has-error' : '' }}">
                    <label for="type" class="col-md-2 form-control-label ml-3 font-weight-bold">Answers order does not matter</label>
                    <div class="col-md-8 radio">
                        <label for="type" class="col-md-3">
                            <input id='question_order_input' type="checkbox" name="question_order" value="1"></label>
                        @if ($errors->has('question_order'))
                            <span class="form-text">
                                    <strong>{{ $errors->first('question_order') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row" id='depended_answers' style='display: none;' >
                    <label class="col-md-2 form-control-label ml-3 font-weight-bold"></label>
                    <div class="col-md-8">
                        To make answers depended on each other put "x" for main answer and math expression for other like x+7.
                    </div>
                </div>

                <script type="text/javascript">
                    let reply_mode = document.getElementById('reply_mode');
                    reply_mode.onchange = () => {
                        let order = document.getElementById('question_order');
                        let order_input = document.getElementById('question_order_input');
                        let depended_answers = document.getElementById('depended_answers');
                        if(reply_mode.value == 'FB') {
                            order.style.display = 'flex';
                            depended_answers.style.display = 'flex';
                        }  else {
                            order.style.display = 'none';
                            order_input.value = false;
                            depended_answers.style.display = 'none';
                        }
                    }
                </script>
                <div class="answers_block">
                    <div class="form-group row answer{{ $errors->has('answer.0') ? ' has-error' : '' }}">
                        <label for="answer" class="col-md-2 form-control-label ml-3 font-weight-bold">Answer</label>
                        <div class="col-md-2">
                            <input class="form-control answer-input" name="answer[]" value="{{ old('answer.0') }}" id = "answer1">
                            <select class ="form-control answer-TF" >
                                <option value="True">True</option>
                                <option value="False">False</option>
                            </select>
                            @if ($errors->has('answer.0'))
                                <span class="form-text">
                                            <strong>Answer can't be empty.</strong>
                                        </span>
                            @endif
                        </div>
                        <div class="col-md-1">
                            <div class="radio">
                                <label class="align-middle m-0">
                                    <input type="checkbox" name="is_correct[]" class="align-middle" value="0" {{ old('is_correct') == 0 ? ' checked' : '' }}>
                                </label>
                            </div>
                        </div>
                    </div>
                    @for ($i = 1; $i < 6; $i++)
                        @if ($errors->has('answer.' . $i) || old('answer.' . $i) != '')
                            <div class="form-group row answer{{ $errors->has('answer.' . $i) ? ' has-error' : '' }}">
                                <label for="answer" class="col-md-2 form-control-label ml-3 font-weight-bold">Answer</label>
                                <div class="col-md-2">
                                    <input class="form-control answer-input" name="answer[]" value="{{ old('answer.' . $i) }}">
                                    @if ($errors->has('answer.' . $i))
                                        <span class="form-text">
                                                    <strong>Answer can't be empty.</strong>
                                                </span>
                                    @endif
                                </div>
                                <div class="col-md-1">
                                    <div class="radio">
                                        <label>
                                            <input type="checkbox" name="is_correct[]" value="{{ $i }}"{{ old('is_correct') == $i ? ' checked' : '' }}>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger delete_line">X</button>
                                </div>
                            </div>
                        @endif
                    @endfor
                </div>
                <div class="form-group row add_answer_block" style="display: none;">
                    <label for="answer" class="col-md-8 form-control-label"></label>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-info pull-right add_answer">+ add answer</button>
                    </div>
                    <div class="col-md-2"></div>
                </div>
                <div class="form-group row">
                    <label for="question" class="col-md-2 form-control-label ml-3 font-weight-bold">Preview</label>
                    <div class="col-md-8">
                        <div class="preview"></div>
                        <button type="button"  data-toggle="modal"
                                data-target="#previewModal"
                                class="btn btn-info pull-right preview_button mt-2">
                            Preview question
                        </button>
                        <span id='preview_url' style="display: none !important;">
                                    {{ $preview_url }}
                                </span>
                    </div>
                </div>

                <div class="form-group row{{ $errors->has('explanation') ? ' has-error' : '' }}">
                    <label for="explanation" class="col-md-2 form-control-label ml-3 font-weight-bold">Explanation</label>

                    <div class="col-md-8">
                        <textarea id="explanation" class="form-control"  name="explanation" placeholder="Enter explanation text.."></textarea>

                        @if ($errors->has('explanation'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('explanation') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row{{ $errors->has('rounding') ? ' has-error' : '' }}">
                    <label for="rounding" class="col-md-2 form-control-label ml-3 font-weight-bold">User answer rounding</label>
                    <div class="col-md-8 radio">
                        <label for="rounding">
                            <input type="radio" name="rounding" value="0" checked="checked"/>
                            Do not round <br>
                            <input type="radio" name="rounding" value="1"/>
                            Round user answer with same precision as correct answer <br>
                            <input type="radio" name="rounding" value="2" />
                            Round Answers up to N digits after point <br>
                        </label>
                        @if ($errors->has('rounding'))
                            <span class="form-text">
                                    <strong>{{ $errors->first('rounding') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
                <div id="answers-round" class="form-group row {{ $errors->has('answers_round') ? ' has-error' : '' }}" style="display: none;">
                    <label for="answers_round" class="col-md-2 form-control-label ml-3 font-weight-bold">Round answers up to N digits after point</label>
                    <div class="col-md-8">
                        <input id="answers_round" class="form-control" name="answers_round" value="2" />
                    </div>
                </div>
                <input type="hidden" name="correct_answer" value="1">
        </div>
            <div class="card-footer">
                <a class="btn btn-secondary" href="{{ route('questions.index') }}">Back</a>
                <button class="btn btn-dark" type="submit" >Submit</button>
            </div>
        </form>
    </div>

<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" id="previewModalTitle">Question Preview</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <iframe width="100%" height="500px" frameborder="0" allowfullscreen></iframe>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
    <script src="{{ URL::asset('js/jquery.jslatex.packed.js') }}"></script>
    <script src="{{ URL::asset('js/ckeditor/ckeditor.js') }}"></script>
    <script>
        var question = CKEDITOR.replace('question');
        var explanation = CKEDITOR.replace('explanation');
    </script>
    <script>
        $(document).ready(function(){
            setTimeout(function() {
                $('#reply_mode').trigger('change');
            }, 0);
            setTimeout(function() {
                $('#successMessage').fadeOut('fast');
            }, 4000); // <-- time in milliseconds

            $('.add_answer').on('click', function() {
                add_answer(1);
                $('#reply_mode').trigger('change');
            });


            $('.preview_button').on('click', function() {
                let data = {};
                let formData = $('#create-form').serializeArray();
                $.each(formData, function() {
                    if (data[this.name]) {
                        if (!data[this.name].push) {
                            data[this.name] = [data[this.name]];
                        }
                        data[this.name].push(this.value || '');
                    } else {
                        data[this.name] = this.value || '';
                    }
                });
                let src = document.getElementById('preview_url').innerHTML;
                src = src.replace(/\s/g, '');
                src += 'question?reply_mode='
                    + data["reply_mode"] + '&question='
                    + encodeURIComponent(question.getData());
                let answers = data["answer[]"];
                if(Array.isArray(answers)) {
                    for(let i = 0; i < answers.length; i++) {
                        src += '&answer' + (i+1) + '=' + encodeURIComponent(answers[i]);
                    }
                } else {
                    src += '&answer1=' + encodeURIComponent(answers);
                }
                $('.modal').on('shown.bs.modal', function(){
                    $(this).find('iframe').attr('src', src)
                });
            });


            $(document).on('change', '[name="is_correct[]"]', function () {
                var val = $('#reply_mode').val();
                var el = $(this);
                if (val == 'general') {
                    $('[name="is_correct[]"]').each(function(index, element) {
                        $(element).prop('checked', true);
                    });
                }
                if (val == 'FB') {
                    $('[name="is_correct[]"]').each(function(index, element) {
                        $(element).prop('checked', true);
                    });
                }
                if (val == 'TF') {
                    $('[name="is_correct[]"]').each(function(index, element) {
                        $(element).prop('checked', true);
                    });
                }
                if (val == 'mcq') {
                    $('[name="is_correct[]"]:checked').prop('checked', false);
                    el.prop('checked', true);
                }
                if (val == 'mcqms') {
                    if ($('[name="is_correct[]"]:checked').length == 0) {
                        el.prop('checked', true);
                    }
                }
                if (val == 'order') {
                    $('[name="is_correct[]"]').each(function(index, element) {
                        $(element).prop('checked', true);
                    });
                }
            });

            $(document).on('click', '.delete_line', function() {
                $(this).closest('.answer').remove();
                $('.add_answer_block').show();
                $('.answer [type="radio"]').each(function(index, element) {
                    $(element).val(index);
                });
                latex_generate();
            });

            $('#reply_mode').on('change', function() {
                var val = $(this).val();
                if (val == 'general') {
                    manage_answer(1, 1);
                }
                if (val == 'FB') {
                    manage_answer(1, 6);
                }
                if (val == 'TF') {
                    manage_answer(1, 1);
                }
                if (val == 'mcq') {
                    manage_answer(2, 6);
                }
                if (val == 'mcqms') {
                    manage_answer(2, 6);
                }
                if (val == 'order') {
                    manage_answer(2, 6);
                }
            });

            question.on('change', function() {
                latex_generate();
            });

            $(document).on('keyup', '[name="answer[]"]', function() {
                latex_generate();
            });

            $("input[name='rounding']").change( function () {
                let answersRound = document.getElementById("answers-round");
                if($("input[name='rounding']:checked").val() === '2') {
                    answersRound.style.display = 'flex';
                    if(!answersRound.value) answersRound.value = 2;
                } else {
                    answersRound.style.display = 'none';
                }
            });
        });

        function latex_generate() {
            $('.preview').text('');
            $.each($('[name="question"]'), function() {
                $('.preview').append('<label class="font-weight-bold">Question</label><div>' + question.getData() + '</div>');
            });
            $.each($('[name="answer[]"]'), function(key) {
                $('.preview').append('<label class="font-weight-bold">Answer ' + (key+1) + '</label><div>' + $(this).val() + '</div>');
            });
            $('.latex').latex();
            MathJax.Hub.Typeset();
        }

        function manage_answer(min, max) {
            if ($('.answers_block .answer').length < min) {
                add_answer(0);
            }
            $.each($('.answers_block').find('.answer'), function(key, value){
                if ((key+1) <= min) {
                    $(this).find('.delete_line').hide();
                }
                if ((key+1) > max) {
                    $(this).remove();
                }
            });
            if ($('.answers_block .answer').length == max) {
                $('.add_answer_block').hide();
            } else {
                $('.add_answer_block').show();
            }
            if ($('[name="is_correct[]"]:checked').length) {
                $('[name="is_correct[]"]:checked').trigger('change');
            } else {
                $('[name="is_correct[]"]').trigger('change');
            }
            latex_generate();
        }

        function add_answer(remove) {
            var block_remove;
            if (remove == 1) {
                block_remove = '                                <div class="col-md-1">\n' +
                    '                                    <button type="button" class="btn btn-danger delete_line">X</button>' +
                    '                                </div>\n';
            } else {
                block_remove = '';
            }
            $('.answers_block').append('<div class="form-group row answer">\n' +
                '                                <label for="answer" class="col-md-2 form-control-label ml-3 font-weight-bold"></label>\n' +
                '                                <div class="col-md-2">\n' +
                '                                    <input class="form-control" name="answer[]">\n' +
                '                                </div>\n' +
                '                                <div class="col-md-1">\n' +
                '                                    <div class="radio">\n' +
                '                                        <label>\n' +
                '                                            <input type="checkbox" name="is_correct[]" value="' + $('.answers_block .answer').length + '" checked>\n' +
                '                                        </label>\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                block_remove      +
                '                            </div>');
            $('[name="is_correct[]"]').trigger('change');
            latex_generate()
        }
    </script>

@endsection

@section('styles')
    <style>
        @media screen and (max-width: 600px) {
            .col-md-8, .form-group row.answer > div {
                margin: 0 16px;
            }
        }
    </style>
@endsection
