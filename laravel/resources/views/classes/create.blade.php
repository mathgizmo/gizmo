@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Classes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('classes.index')  }}">Manage Classes</a></li>
    <li class="breadcrumb-item active">Create Class</li>
@endsection

@section('content')
    @if(Session::has('flash_message'))
        <div id="successMessage" class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>
            <em> {!! session('flash_message') !!}</em>
        </div>
    @endif
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Create Class</div>
        <form role="form" action="{{ route('classes.store') }}" method="POST">
            <div class="card-body p-0">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group row mt-3 {{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name" class="col-md-2 form-control-label ml-3 font-weight-bold">Name</label>
                    <div class="col-md-8">
                        <input id="name" class="form-control" name="name"
                                  placeholder="Enter Class Name" value="{{ old('name') }}"/>
                        @if ($errors->has('name'))
                            <span class="form-text">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row mt-3 {{ $errors->has('teacher_id') ? ' has-error' : '' }}">
                    <label for="teacher_id" class="col-md-2 form-control-label ml-3 font-weight-bold">Teacher</label>
                    <div class="col-md-8">
                        <input id="teacher-input" class="form-control" name="teacher" list="teachers-datalist"
                               placeholder="Enter Class Teacher" value="{{ old('teacher') }}"/>
                        <datalist id="teachers-datalist"></datalist>
                        <input id="teacher_id" name="teacher_id" type="hidden"/>
                        @if ($errors->has('teacher_id'))
                            <span class="form-text">
                                <strong>{{ $errors->first('teacher_id') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row mt-3 {{ $errors->has('class_type') ? ' has-error' : '' }}">
                    <label for="class_type" class="col-md-2 form-control-label ml-3 font-weight-bold">Type of classroom</label>
                    <div class="col-md-8">
                        <select id="class_type" name="class_type" class="form-control" style="max-width: 200px;">
                            <option value="elementary">Elementary</option>
                            <option value="secondary">Secondary</option>
                            <option value="college">College</option>
                            <option value="university">University</option>
                            <option value="professional">Professional</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row mt-3 {{ $errors->has('subscription_type') ? ' has-error' : '' }}">
                    <label for="subscription_type" class="col-md-2 form-control-label ml-3 font-weight-bold">Subscription Type</label>
                    <div class="col-md-8">
                        <select id="subscription_type" name="subscription_type" class="form-control" style="max-width: 200px;">
                            <option value="open" selected="selected">Open</option>
                            <option value="invitation">Invitation Only</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </div>
                <div id="invitations-container" class="form-group row mt-3" style="display: none;">
                    <label for="invitations" class="col-md-2 form-control-label ml-3 font-weight-bold">Invitations</label>
                    <div class="col-md-8">
                        <textarea id="invitations" class="form-control" name="invitations"
                                  placeholder="Please enter comma separated email list">{{ old('invitations') }}</textarea>
                    </div>
                </div>

                <div class="form-group row mt-3">
                    <label for="assignment-input" class="col-md-2 form-control-label ml-3 font-weight-bold">Assignments</label>
                    <div class="col-md-8">
                        <input id="assignment-input" class="form-control" name="assignment-input" list="assignments-datalist"
                               placeholder="Enter Assignment Name" />
                        <datalist id="assignments-datalist"></datalist>
                        <ul id="assignments" class="list-group mt-3"></ul>
                    </div>
                </div>

                <div class="form-group row mt-3">
                    <label for="test-input" class="col-md-2 form-control-label ml-3 font-weight-bold">Tests</label>
                    <div class="col-md-8">
                        <input id="test-input" class="form-control" name="test-input" list="tests-datalist"
                               placeholder="Enter Test Name" />
                        <datalist id="tests-datalist"></datalist>
                        <ul id="tests" class="list-group mt-3"></ul>
                    </div>
                </div>

            </div>

            <div class="card-footer">
                <a class="btn btn-secondary" href="{{ route('classes.index') }}">Back</a>
                <button class="btn btn-dark" type="submit">Submit</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')

    <script>
        $(document).ready(function () {
            setTimeout(function () {
                $('#successMessage').fadeOut('fast');
            }, 4000); // <-- time in milliseconds

            $("#subscription_type").change(function () {
                let invitations = document.getElementById("invitations-container");
                if ($(this).val() === 'invitation') {
                    invitations.style.display = 'flex';
                } else {
                    invitations.style.display = 'none';
                }
            });

            $('#teacher-input').keyup(function() {
                const pattern = $('#teacher-input').val();
                if(pattern) {
                    $.ajax({
                        url: "{{route('students.search')}}?pattern="+pattern+'&is_teacher=1&limit=5',
                        type: "GET",
                        success: function(data, textStatus, jqXHR) {
                            const dl = document.getElementById('teachers-datalist');
                            dl.innerHTML = '';
                            data.forEach((item, index) => {
                                const option = document.createElement('option');
                                option.setAttribute('key', item.id);
                                option.value = item.name;
                                dl.appendChild(option);
                            });
                        }
                    });
                }
            }).change(function () {
                setTimeout(() => {
                    const options = document.getElementById("teachers-datalist").options;
                    if (options.length > 0) {
                        const option = options.item(0);
                        const teacher_id = document.getElementById('teacher_id');
                        teacher_id.value = option.getAttribute('key');
                    }
                }, 200);
            });

            $('#assignment-input').keyup(function() {
                const pattern = $('#assignment-input').val();
                if(pattern) {
                    $.ajax({
                        url: "{{route('applications.search')}}?pattern="+pattern+'&type=assignment&limit=5',
                        type: "GET",
                        success: function(data, textStatus, jqXHR) {
                            const dl = document.getElementById('assignments-datalist');
                            dl.innerHTML = '';
                            data.forEach((item, index) => {
                                const option = document.createElement('option');
                                option.setAttribute('key', item.id);
                                option.value = item.name;
                                dl.appendChild(option);
                            });
                        }
                    });
                }
            }).change(function () {
                setTimeout(() => {
                    const options = document.getElementById("assignments-datalist").options;
                    if (options.length > 0) {
                        const option = options.item(0);
                        const li = document.createElement('li');
                        li.classList.add('list-group-item');
                        li.classList.add('d-flex');
                        li.classList.add('flex-row');
                        li.classList.add('justify-content-between');
                        li.classList.add('align-items-center');
                        const name = document.createElement('a');
                        name.classList.add('font-weight-bold');
                        name.classList.add('text-decoration-none');
                        name.classList.add('text-dark');
                        name.setAttribute('href', '../../applications/'+option.getAttribute('key')+'/edit');
                        name.setAttribute('target', '_blank');
                        name.innerHTML = option.value;
                        li.appendChild(name);
                        const container = document.createElement('div');
                        container.classList.add('d-flex');
                        container.classList.add('flex-row');
                        container.classList.add('align-items-center');
                        const dateLabel = document.createElement('div');
                        dateLabel.classList.add('mr-2');
                        dateLabel.style.minWidth = '80px';
                        dateLabel.innerHTML = 'Due Date:';
                        container.appendChild(dateLabel);
                        const dateInput = document.createElement('input');
                        dateInput.classList.add('form-control');
                        dateInput.classList.add('mr-3');
                        dateInput.setAttribute('type', 'date');
                        dateInput.setAttribute('name', 'assignment['+option.getAttribute('key')+'][due_date]');
                        container.appendChild(dateInput);
                        const removeBtn = document.createElement('i');
                        removeBtn.classList.add('fas');
                        removeBtn.classList.add('fa-trash-alt');
                        removeBtn.style.cursor = 'pointer';
                        removeBtn.setAttribute('onclick', 'this.parentNode.parentNode.remove();');
                        container.appendChild(removeBtn);
                        li.appendChild(container);
                        document.getElementById('assignments').appendChild(li);
                        $(this).val('');
                    }
                }, 200);
            });

            $('#test-input').keyup(function() {
                const pattern = $('#test-input').val();
                if(pattern) {
                    $.ajax({
                        url: "{{route('applications.search')}}?pattern="+pattern+'&type=test&limit=5',
                        type: "GET",
                        success: function(data, textStatus, jqXHR) {
                            const dl = document.getElementById('tests-datalist');
                            dl.innerHTML = '';
                            data.forEach((item, index) => {
                                const option = document.createElement('option');
                                option.setAttribute('key', item.id);
                                option.value = item.name;
                                dl.appendChild(option);
                            });
                        }
                    });
                }
            }).change(function () {
                setTimeout(() => {
                    const options = document.getElementById("tests-datalist").options;
                    if (options.length > 0) {
                        const option = options.item(0);
                        const li = document.createElement('li');
                        li.classList.add('list-group-item');
                        li.classList.add('d-flex');
                        li.classList.add('flex-row');
                        li.classList.add('justify-content-between');
                        li.classList.add('align-items-center');
                        const name = document.createElement('a');
                        name.classList.add('font-weight-bold');
                        name.classList.add('text-decoration-none');
                        name.classList.add('text-dark');
                        name.setAttribute('href', '../../applications/'+option.getAttribute('key')+'/edit');
                        name.setAttribute('target', '_blank');
                        name.innerHTML = option.value;
                        li.appendChild(name);
                        const container = document.createElement('div');
                        container.classList.add('d-flex');
                        container.classList.add('flex-row');
                        container.classList.add('align-items-center');
                        const dateLabel = document.createElement('div');
                        dateLabel.classList.add('mr-2');
                        dateLabel.style.minWidth = '80px';
                        dateLabel.innerHTML = 'Due Date:';
                        container.appendChild(dateLabel);
                        const dateInput = document.createElement('input');
                        dateInput.classList.add('form-control');
                        dateInput.classList.add('mr-3');
                        dateInput.setAttribute('type', 'date');
                        dateInput.setAttribute('name', 'test['+option.getAttribute('key')+'][due_date]');
                        container.appendChild(dateInput);
                        const removeBtn = document.createElement('i');
                        removeBtn.classList.add('fas');
                        removeBtn.classList.add('fa-trash-alt');
                        removeBtn.style.cursor = 'pointer';
                        removeBtn.setAttribute('onclick', 'this.parentNode.parentNode.remove();');
                        container.appendChild(removeBtn);
                        li.appendChild(container);
                        document.getElementById('tests').appendChild(li);
                        $(this).val('');
                    }
                }, 200);
            });
        });

    </script>

@endsection

@section('styles')
    <style>
        input[type='date'] {
            max-width: 200px;
        }
        @media screen and (max-width: 600px) {
            .col-md-8 {
                margin: 0 16px;
            }
        }
    </style>
@endsection
