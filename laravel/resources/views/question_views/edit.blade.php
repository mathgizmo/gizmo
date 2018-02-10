@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Question / Edit</div>

                <div class="panel-body">

    <div class="row">
        <div class="col-md-12">

            <form class="form-horizontal" role="form" action="{{ route('question_views.update', $question->id) }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

				        <div class="form-group{{ $errors->has('level_id') ? ' has-error' : '' }}">
                            <label for="level_id" class="col-md-4 control-label">Level</label>

                            <div class="col-md-6">
							      <select class="form-control" name="level_id" id="level_id">

								  @if (count($levels) > 0)
									  <option value="">Select From ...</option>
										@foreach($levels as $level)
											<option value="{{$level->id}}" @if (old("level_id") == $level->id) selected="selected" @endif  @if ( $level->id == $question->lid) selected="selected"
											@endif
											>{{$level->title}}</option>
										@endforeach
								 @endif
									</select>

                                @if ($errors->has('level_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('level_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

						<div id="unit_options"> </div>
						<div class="form-group{{ $errors->has('unit_id') ? ' has-error' : '' }}">
                            <label for="unit_id" class="col-md-4 control-label">Unit</label>

                            <div class="col-md-6">
							      <select class="form-control" name="unit_id" id="unit_id">
								   @if (count($units) > 0)
                                          <option value="">Select From ...</option>
								  @foreach($units as $unit)
											<option value="{{$unit->id}}" @if ( $unit->id == $question->uid) selected="selected"
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

						<div class="form-group{{ $errors->has('topic_id') ? ' has-error' : '' }}">
                            <label for="topic_id" class="col-md-4 control-label">Topic</label>

                            <div class="col-md-6">
							      <select class="form-control" name="topic_id" id="topic_id">

								  @if (count($topics) > 0)
                                          <option value="">Select From ...</option>
								  @foreach($topics as $topic)
											<option value="{{$topic->id}}" @if ( $topic->id == $question->tid) selected="selected"
											@endif
											>{{$topic->title}}</option>
										@endforeach
								  @endif

									</select>

                                @if ($errors->has('topic_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('topic_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

						<div class="form-group{{ $errors->has('lesson_id') ? ' has-error' : '' }}">
                            <label for="lesson_id" class="col-md-4 control-label">Lesson</label>

                            <div class="col-md-6">
							      <select class="form-control" name="lesson_id" id="lesson_id">

									@if (count($lessons) > 0)
                                          <option value="">Select From ...</option>
								  @foreach($lessons as $lesson)
											<option value="{{$lesson->id}}" @if ( $lesson->id == $question->lesson_id) selected="selected"
											@endif
											>{{$lesson->title}}</option>
										@endforeach
								  @endif
									</select>

                                @if ($errors->has('lesson_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lesson_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('reply_mode') ? ' has-error' : '' }}">
                            <label for="reply_mode" class="col-md-4 control-label">REPLY MODE</label>

                            <div class="col-md-6">
                                  <select class="form-control" name="reply_mode"id="reply_mode">
                                         @foreach($qrmodes as $qrmode)
                                                @if (old('reply_mode') == $qrmode->code || $question->reply_mode == $qrmode->code)
                                                    <option value="{{$qrmode->code}}" selected>{{$qrmode->mode}}</option>
                                                @else
                                                    <option value="{{$qrmode->code}}">{{$qrmode->mode}}</option>
                                                @endif

                                        @endforeach
                                    </select>

                                @if ($errors->has('reply_mode'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('reply_mode') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						<div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
                            <label for="question" class="col-md-4 control-label">Question</label>
                            <div class="col-md-6">
                                <span>For LaTeX please use next format \( $$latext here$$ \) or \(\(latext here\)\)</span>
                                <br />
                                <span>Use __ as placeholder in Fill in the blank questions</span>
                                <textarea id="question" class="form-control"  name="question"> {{$question->question}}</textarea>

                                @if ($errors->has('question'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('question') }}</strong>
                                    </span>
                                @endif
                            </div>
				      </div>
                    <div class="answers_block">
		    	<?php $key = 0; ?>
                        @foreach($answers as $key => $answer)
                            <div class="form-group answer{{ $errors->has('answer.' . $key) ? ' has-error' : '' }}">
                                <label for="answer" class="col-md-4 control-label">Answer</label>
                                <div class="col-md-4">
                                    <input class="form-control answer-input" name="answer[]" value="{{ old('answer.' . $key) ?? $answer->value }}">
                                    @if ($key == 0)
                                    <select class ="form-control answer-TF" >
                                        <option value="True">True</option>
                                        <option value="False">False</option>
                                    </select>
                                    @endif
                                    @if ($errors->has('answer.' . $key))
                                        <span class="help-block">
                                                    <strong>Answer can't be empty.</strong>
                                                </span>
                                    @endif
                                </div>
                                <div class="col-md-1">
                                    <div class="radio">
                                        <label>
                                            <input type="checkbox" name="is_correct[]" value="{{ $key }}"{{ $answer->is_correct ? ' checked' : ''}}>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                   <button type="button" class="btn btn-danger delete_line">X</button>
                                </div>
                            </div>
                        @endforeach
                            @for ($i = $key; $i < 6; $i++)
                                @if ($errors->has('answer.' . $i) || old('answer.' . $i) != '' )
                                    <div class="form-group answer{{ $errors->has('answer.' . $i) ? ' has-error' : '' }}">
                                        <label for="answer" class="col-md-4 control-label">Answer</label>
                                        <div class="col-md-4">
                                            <input class="form-control answer-input" name="answer[]" value="{{ old('answer.' . $i) }}">
                                            @if ($key == 0)
                                            <select class ="form-control answer-TF" >
                                                <option value="True">True</option>
                                                <option value="False">False</option>
                                            </select>
                                            @endif
                                            @if ($errors->has('answer.' . $i))
                                                <span class="help-block">
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

                    <div class="form-group add_answer_block" style="display: none;">
                        <label for="answer" class="col-md-6 control-label"></label>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-info pull-right add_answer">+ add answer</button>
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                    <div class="form-group">
                        <label for="question" class="col-md-4 control-label">Preview</label>
                        <div class="col-md-6">
                            <div class="preview"></div>
                        </div>
                    </div>

					  <div class="form-group{{ $errors->has('feedback') ? ' has-error' : '' }}">
                            <label for="feedback" class="col-md-4 control-label">Feedback</label>

                            <div class="col-md-6">
                                <textarea id="feedback" class="form-control"  name="feedback"> {{$question->feedback}}</textarea>

                                @if ($errors->has('feedback'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('feedback') }}</strong>
                                    </span>
                                @endif
                            </div>
				      </div>

					  <div class="form-group{{ $errors->has('explanation') ? ' has-error' : '' }}">
                            <label for="explanation" class="col-md-4 control-label">Explanation</label>

                            <div class="col-md-6">
                                <textarea id="explanation" class="form-control"  name="explanation">{{$question->explanation}}</textarea>

                                @if ($errors->has('explanation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('explanation') }}</strong>
                                    </span>
                                @endif
                            </div>
				      </div>
                <div class="form-group{{ $errors->has('rounding') ? ' has-error' : '' }}">
                    <label for="type" class="col-md-4 control-label">Round user answer with same precision as correct answer</label>

                    <div class="col-md-6 radio">
                        <label for="type" class="col-md-3"> <input {{($question->rounding == true) ? 'checked="checked"' : ''}} type="checkbox" name="rounding" value="1"></label>
                        @if ($errors->has('rounding'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('rounding') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('conversion') ? ' has-error' : '' }}">
                    <label for="type" class="col-md-4 control-label">Convert user answer to decimal value</label>

                    <div class="col-md-6 radio">
                        <label for="type" class="col-md-3"> <input {{($question->conversion == true) ? 'checked="checked"' : ''}} type="checkbox" name="conversion" value="1"></label>
                        @if ($errors->has('conversion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('conversion') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>



            <a class="btn btn-default" href="{{ route('question_views.index') }}">Back</a>
            <button class="btn btn-primary" type="submit" >Save</button>
            </form>
        </div>
    </div>

	          </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('js/jquery.jslatex.packed.js') }}"></script>
    <script src="{{ URL::asset('js/ckeditor/ckeditor.js') }}"></script>
    <script>
        var question = CKEDITOR.replace( 'question' );
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
        });

        function latex_generate() {
            $('.preview').text('');
            $.each($('[name="question"]'), function() {
                $('.preview').append('<label>Question</label><div>' + question.getData() + '</div>');
            });
            $.each($('[name="answer[]"]'), function(key) {
                $('.preview').append('<label>Answer ' + (key+1) + '</label><div>' + $(this).val() + '</div>');
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
            $('.answers_block').append('<div class="form-group answer">\n' +
                '                                <label for="answer" class="col-md-4 control-label"></label>\n' +
                '                                <div class="col-md-4">\n' +
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
