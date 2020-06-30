@extends('layouts.app')

@section('title', 'Gizmo - Admin: Mails')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('mails.index')  }}">Mails</a></li>
    <li class="breadcrumb-item active">Send Mail</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">@lang('settings.new_mail')</div>
        <form role="form" action="{{ route('mails.send') }}" autocomplete="off" method="POST">
            @csrf
            @method('post')
            <div class="card-body p-0">
                <div class="form-group row mt-3 {{ $errors->has('subject') ? ' has-error' : '' }}">
                    <label for="subject" class="col-md-2 form-control-label ml-3 font-weight-bold">@lang('settings.subject')</label>
                    <div class="col-md-8">
                        <input type="text" name="subject" id="subject" class="form-control"
                               value="{{ old('subject') }}">
                        @if ($errors->has('subject'))
                            <span class="form-text">
                                  <strong>{{ $errors->first('subject') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row mt-3">
                    <label class="col-md-2 form-control-label ml-3 font-weight-bold">@lang('settings.available_variables')</label>
                    <div class="col-md-8">
                        <div class="d-flex flex-row flex-wrap">
                            @foreach($available_variables as $variable)
                                <span class="variable mb-1 mr-1"> {{ $variable }}</span>
                            @endforeach
                        </div>
                        <div id="class-variables" class="flex-row flex-wrap">
                            @foreach($available_class_variables as $variable)
                                <span class="variable mb-1 mr-1"> {{ $variable }}</span>
                            @endforeach
                        </div>
                        <label class="info mt-2">@lang('settings.variables_usage')</label>
                    </div>
                </div>
                <div class="form-group row mt-3 {{ $errors->has('body') ? ' has-error' : '' }}">
                    <label for="body" class="col-md-2 form-control-label ml-3 font-weight-bold">@lang('settings.body')</label>
                    <div class="col-md-8">
                        <textarea type="text" name="body" id="body" class="form-control">{{ old('body') }}</textarea>
                        @if ($errors->has('body'))
                            <span class="form-text">
                                  <strong>{{ $errors->first('body') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row mt-3">
                    <label class="col-md-2 form-control-label ml-3 font-weight-bold">Send to</label>
                    <div class="col-md-8">
                        <select id="send-to-select" name="send_to" class="form-control">
                            <option value="student">Students</option>
                            <option value="class">Classes</option>
                        </select>
                    </div>
                </div>

                <div id="student-container" class="form-group row mt-3">
                    <label for="input-student" class="col-md-2 form-control-label ml-3 font-weight-bold">Students</label>
                    <div class="col-md-8">
                        <div id="student-select">
                            <div class="input-group">
                                <input id="input-student" name="student-input" class="form-control" placeholder="Search students..." />
                            </div>
                            <div id="students-list" class="d-flex flex-row flex-wrap mt-2"></div>
                        </div>
                        <div>
                            <input id="for-all-students-checkbox" name="for_all_students" type="checkbox">
                            For all students
                        </div>
                    </div>
                </div>

                <div id="class-container" class="form-group row mt-3">
                    <label for="input-class" class="col-md-2 form-control-label ml-3 font-weight-bold">Classes</label>
                    <div class="col-md-8">
                        <div class="input-group">
                            <input id="input-class" name="class-input" class="form-control" placeholder="Search classes..." />
                        </div>
                        <div id="classes-list" class="d-flex flex-row flex-wrap mt-2"></div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <a class="btn btn-secondary"
                   href="{{ route('mails.index') }}">Back</a>
                <button class="btn btn-dark" type="submit" onclick="document.getElementById('loading').style.display = 'flex';">Send</button>
            </div>
        </form>
    </div>

    <div id="loading" style="display: none;">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
        <p>Please, wait! We are sending emails!</p>
    </div>
@endsection

@section('styles')
    <link href="{{ asset('css/spinner.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/jquery-ui.theme.min.css') }}" rel="stylesheet" />
    <style>
        #class-variables, #class-container {
            display: none;
        }
        .ck-content {
            min-height: 400px !important;
        }
        .info {
            background-color: #f0f7fb;
            border-left: solid 4px #3498db;
            line-height: 18px;
            padding: 10px;
            width: 100%;
        }
        .variable {
            font-weight: bolder;
            background-color: rgba(0, 0, 0, 0.03);
            padding: 8px;
        }
        .fa-times {
            cursor: pointer;
        }
    </style>
@endsection

@section('scripts')
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script>
        var studentIndex = 0;
        var classIndex = 0;

        $(document).ready(function () {
            CKEDITOR.replace('body', { toolbar : [ ['Bold','Italic','Font','FontSize'] ] });

            $('#send-to-select').change(function () {
                if (this.value == 'class') {
                    $('#class-variables').css('display', 'flex');
                    $('#class-container').css('display', 'flex');
                    $('#student-container').hide();
                } else {
                    $('#class-variables').hide();
                    $('#class-container').hide();
                    $('#student-container').css('display', 'flex');
                }
            });

            $('#for-all-students-checkbox').change(function () {
                if (this.checked) {
                    $('#student-select').hide();
                } else {
                    $('#student-select').show();
                }
            })

            $(document).on('click','.fa-times',function(){
                $(this).parents('.variable').remove();
            })

            $("#input-student").autocomplete({
                'source': function (request, response) {
                    const pattern = $('#input-student').val();
                    if (pattern) {
                        $.ajax({
                            url: "{{ route('students.search') }}?pattern=" + pattern + '&limit=5',
                            type: "GET",
                            success: function (data, textStatus, jqXHR) {
                                response($.map(data, function (item) {
                                    return {
                                        id: item.id,
                                        value: item.name,
                                        label: item.last_name ? (item.first_name + ' ' + item.last_name) : item.name
                                    }
                                }));
                            }
                        });
                    }
                },
                'select': function (event, item) {
                    const container = document.getElementById('students-list');
                    const student = document.createElement('div');
                    student.classList.add('variable');
                    student.classList.add('mb-1');
                    student.classList.add('mr-1');
                    student.innerHTML = item.item.label;
                    const studId = document.createElement('input');
                    studId.type = 'hidden';
                    studId.name = 'student[' + studentIndex + ']';
                    studId.value = item.item.id;
                    student.appendChild(studId);
                    const closeBtn = document.createElement('i');
                    closeBtn.classList.add('ml-2');
                    closeBtn.classList.add('fas');
                    closeBtn.classList.add('fa-times');
                    student.appendChild(closeBtn);
                    container.appendChild(student);
                    studentIndex++;
                    $('#input-student').val('');
                    return false;
                }
            });

            $("#input-class").autocomplete({
                'source': function (request, response) {
                    const pattern = $('#input-class').val();
                    if (pattern) {
                        $.ajax({
                            url: "{{ route('classes.search') }}?pattern=" + pattern + '&limit=5',
                            type: "GET",
                            success: function (data, textStatus, jqXHR) {
                                response($.map(data, function (item) {
                                    return {
                                        id: item.id,
                                        value: item.name,
                                        label: item.name
                                    }
                                }));
                            }
                        });
                    }
                },
                'select': function (event, item) {
                    const container = document.getElementById('classes-list');
                    const classItm = document.createElement('div');
                    classItm.classList.add('variable');
                    classItm.classList.add('mb-1');
                    classItm.classList.add('mr-1');
                    classItm.innerHTML = item.item.label;
                    const classId = document.createElement('input');
                    classId.type = 'hidden';
                    classId.name = 'class[' + classIndex + ']';
                    classId.value = item.item.id;
                    classItm.appendChild(classId);
                    const closeBtn = document.createElement('i');
                    closeBtn.classList.add('ml-2');
                    closeBtn.classList.add('fas');
                    closeBtn.classList.add('fa-times');
                    classItm.appendChild(closeBtn);
                    container.appendChild(classItm);
                    classIndex++;
                    $('#input-class').val('');
                    return false;
                }
            });
        });

    </script>
@endsection
