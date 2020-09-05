@extends('layouts.app')

@section('title', 'Gizmo - Admin: Manage Classes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('classes.index')  }}">Manage Classes</a></li>
    <li class="breadcrumb-item active">Edit Class</li>
@endsection

@section('content')
    @if(Session::has('flash_message'))
        <div id="successMessage" class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>
            <em> {!! session('flash_message') !!}</em>
        </div>
    @endif
    <div class="card">
        <div class="card-header font-weight-bold d-flex flex-row">Edit Class</div>
        <form role="form" action="{{ route('classes.update', $class->id) }}" method="POST">
            <div class="card-body p-0">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group row mt-3 {{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name" class="col-md-2 form-control-label ml-3 font-weight-bold">Name</label>
                    <div class="col-md-8">
                        <input id="name" class="form-control" name="name"
                               placeholder="Enter Class Name" value="{{ old('name', $class->name) }}"/>
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
                               placeholder="Enter Class Teacher" value="{{ old('teacher', $class->teacher ? $class->teacher->name : '') }}"/>
                        <datalist id="teachers-datalist"></datalist>
                        <input id="teacher_id" name="teacher_id" type="hidden" value="{{old('teacher_id', $class->teacher_id)}}"/>
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
                            <option value="elementary" {{ $class->class_type == 'elementary' ? 'selected="selected"' : '' }}>Elementary</option>
                            <option value="secondary" {{ $class->class_type == 'secondary' ? 'selected="selected"' : '' }}>Secondary</option>
                            <option value="college" {{ $class->class_type == 'college' ? 'selected="selected"' : '' }}>College</option>
                            <option value="university" {{ $class->class_type == 'university' ? 'selected="selected"' : '' }}>University</option>
                            <option value="professional" {{ $class->class_type == 'professional' ? 'selected="selected"' : '' }}>Professional</option>
                            <option value="other" {{ $class->class_type == 'other' ? 'selected="selected"' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row mt-3 {{ $errors->has('subscription_type') ? ' has-error' : '' }}">
                    <label for="subscription_type" class="col-md-2 form-control-label ml-3 font-weight-bold">Subscription Type</label>
                    <div class="col-md-8">
                        <select id="subscription_type" name="subscription_type" class="form-control" style="max-width: 200px;">
                            <option value="open" {{ $class->subscription_type == 'open' ? 'selected="selected"' : '' }}>Open</option>
                            <option value="invitation" {{ $class->subscription_type == 'invitation' ? 'selected="selected"' : '' }}>Invitation Only</option>
                            <option value="closed" {{ $class->subscription_type == 'closed' ? 'selected="selected"' : '' }}>Closed</option>
                        </select>
                    </div>
                </div>
                <div id="invitations-container" class="form-group row mt-3" style="display: {{ $class->subscription_type == 'invitation' ? 'flex' : 'none' }};">
                    <label for="invitations" class="col-md-2 form-control-label ml-3 font-weight-bold">Invitations</label>
                    <div class="col-md-8">
                        <textarea id="invitations" class="form-control" name="invitations"
                                  placeholder="Please enter comma separated email list">{{ old('invitations', $class->invitations) }}</textarea>
                    </div>
                </div>

                <div class="form-group row mt-3">
                    <label for="application-input" class="col-md-2 form-control-label ml-3 font-weight-bold">Assignments</label>
                    <div class="col-md-8">
                        <input id="application-input" class="form-control" name="applications-input" list="applications-datalist"
                               placeholder="Enter Assignments Name" />
                        <datalist id="applications-datalist"></datalist>
                        <ul id="applications" class="list-group mt-3">
                            @foreach($applications as $app)
                                <li class="list-group-item d-flex flex-row justify-content-between align-items-center">
                                    <a href="{{route('applications.edit', $app->id)}}" target="_blank" class="font-weight-bold text-decoration-none text-dark">{{$app->name}}</a>
                                    <div class="d-flex flex-row align-items-center">
                                        <div class="mr-2" style="min-width: 80px;">Due Date:</div>
                                        <input class="form-control mr-3" type="date" name="application[{{$app->id}}][due_date]" value="{{$app->getDueDate($class->id)}}" />
                                        <i class="fas fa-trash-alt" style="cursor: pointer;" onclick="this.parentNode.parentNode.remove();"></i>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <a class="btn btn-secondary" href="{{ route('classes.index') }}">Back</a>
                <button class="btn btn-dark" type="submit">Save</button>
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

            $('#application-input').keyup(function() {
                const pattern = $('#application-input').val();
                if(pattern) {
                    $.ajax({
                        url: "{{route('applications.search')}}?pattern="+pattern+'&limit=5',
                        type: "GET",
                        success: function(data, textStatus, jqXHR) {
                            const dl = document.getElementById('applications-datalist');
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
                    const options = document.getElementById("applications-datalist").options;
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
                        dateInput.setAttribute('name', 'application['+option.getAttribute('key')+'][due_date]');
                        container.appendChild(dateInput);
                        const removeBtn = document.createElement('i');
                        removeBtn.classList.add('fas');
                        removeBtn.classList.add('fa-trash-alt');
                        removeBtn.style.cursor = 'pointer';
                        removeBtn.setAttribute('onclick', 'this.parentNode.parentNode.remove();');
                        container.appendChild(removeBtn);
                        li.appendChild(container);
                        document.getElementById('applications').appendChild(li);
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
