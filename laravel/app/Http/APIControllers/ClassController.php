<?php

namespace App\Http\APIControllers;

use Barryvdh\DomPDF\Facade as PDF;
use App\Application;
use App\ClassApplication;
use App\ClassOfStudents;
use App\Level;
use App\Progress;
use App\Student;
use App\StudentsTrackingQuestion;
use App\Topic;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClassController extends Controller
{

    private $user;

    public function __construct()
    {
        try {
            $this->user = JWTAuth::parseToken()->authenticate();
            if (!$this->user) {
                abort(401, 'Unauthorized!');
            }
        } catch (\Exception $e) {
            abort(401, 'Unauthorized!');
        }
    }

    public function all() {
        return $this->success([
            'items' => array_values(ClassOfStudents::where('teacher_id', $this->user->id)->get()->toArray())
        ]);
    }

    public function store() {
        try {
            return $this->success([
                'item' => ClassOfStudents::create([
                    'teacher_id' => $this->user->id,
                    'name' => request('name'),
                    'class_type' => request('class_type') ?: 'other',
                    'subscription_type' => request('subscription_type') ?: 'open',
                    'invitations' => request('invitations')
                ])
            ]);
        } catch (\Exception $e) {
            return $this->error('Error.', 404);
        }
    }

    public function update($class_id) {
        try {
            $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
            if ($class) {
                if (request()->has('name')) {
                    $class->name = request('name');
                }
                if (request()->has('class_type')) {
                    $class->class_type = request('class_type');
                }
                if (request()->has('subscription_type')) {
                    $class->subscription_type = request('subscription_type');
                }
                if (request()->has('invitations')) {
                    $class->invitations = request('invitations');
                }
                $class->save();
                return $this->success(['item' => $class]);
            }
        } catch (\Exception $e) {}
        return $this->error('Error.', 404);
    }

    public function delete($class_id) {
        if ($class_id == 1) {
            abort('403', 'Default class can\'t be deleted!');
        }
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            $class->delete();
            DB::table('classes_applications')->where('class_id', $class_id)->delete();
            return $this->success('Ok.');
        }
        return $this->error('Error.', 404);
    }

    public function getStudents($class_id) {
        $show_extra = request()->filled('extra') && request('extra') == 'true' ? true : false;
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            $students = $class->students()->orderBy('email')
                ->get(['students.id', 'students.name', 'students.first_name', 'students.last_name', 'students.email', 'students.is_registered']);
            if ($show_extra) {
                $apps = Application::whereHas('classes', function ($q1) use ($class_id) {
                    $q1->where('classes.id', $class_id);
                })->where('type', 'assignment')->get();
                $now = Carbon::now()->toDateTimeString();
                foreach ($students as $student) {
                    $class_student = DB::table('classes_students')
                        ->where('class_id', $class_id)
                        ->where('student_id', $student->id)->first();
                    $student->test_duration_multiply_by = $class_student ? $class_student->test_duration_multiply_by : 1;
                    $student->is_subscribed = true;
                    $finished_count = 0; $past_due_count = 0;
                    $student_assignments = $apps->keyBy('id');
                    foreach ($apps as $app) {
                        $class_data = $app->getClassRelatedData($class_id);
                        if ($class_data->is_for_selected_students) {
                            if (DB::table('classes_applications_students')
                                    ->where('class_app_id', $class_data->id)
                                    ->where('student_id', $student->id)->count() < 1) {
                                $student_assignments->forget($app->id);
                                continue;
                            }
                        }
                        $app->icon = $app->icon();
                        $app->is_completed = Progress::where('entity_type', 'application')->where('entity_id', $app->id)
                                ->where('student_id', $student->id)->count() > 0;
                        if ($class_data->due_date) {
                            $due_at = $class_data->due_time ? $class_data->due_date.' '.$class_data->due_time : $class_data->due_date.' 00:00:00';
                        } else {
                            $due_at = null;
                        }
                        $app->due_at = $due_at ? Carbon::parse($due_at)->format('Y-m-d g:i A') : null;
                        $completed_at = $app->getCompletedDate($student->id);
                        $app->completed_at = $completed_at ? Carbon::parse($completed_at)->format('Y-m-d g:i A') : null;
                        $app->is_past_due = (!$app->is_completed && $due_at && $due_at < $now) ||
                            ($app->is_completed && $due_at && $completed_at && $due_at < $completed_at);
                        if ($app->is_completed) {
                            $finished_count++;
                        }
                        if ($app->is_past_due) {
                            $past_due_count++;
                        }
                    }
                    $student->assignments = array_values($student_assignments->sortBy('due_date')->toArray());
                    $student->assignments_finished_count = $finished_count;
                    $student->assignments_past_due_count = $past_due_count;
                }
            }
            if ($class->subscription_type == 'invitation' && $show_extra) {
                $emails = explode(',', strtolower(str_replace(' ', '', preg_replace( "/;|\n/", ',', $class->invitations))));
                $not_subscribed = [];
                $assignments_past_due_count = 0;
                foreach ($apps as $app) {
                    $class_data = $app->getClassRelatedData($class_id);
                    if ($class_data->is_for_selected_students) {
                        continue;
                    }
                    if ($class_data->due_date) {
                        $due_at = $class_data->due_time ? $class_data->due_date.' '.$class_data->due_time : $class_data->due_date.' 00:00:00';
                    } else {
                        $due_at = null;
                    }
                    if ($due_at && $due_at < $now) {
                        $assignments_past_due_count++;
                    }
                }
                foreach (array_filter($emails) as $email) {
                    if ($students->where('email', trim($email))->count() < 1) {
                        array_push($not_subscribed, (object) [
                            'name' => $email,
                            'email' => $email,
                            'is_registered' => false,
                            'is_subscribed' => false,
                            'assignments_finished_count' => 0,
                            'assignments_past_due_count' => $assignments_past_due_count
                        ]);
                    }
                }
                $not_sorted_items = array_merge(array_values($students->toArray()), $not_subscribed);
                $items = array_values(collect($not_sorted_items)->sortBy('email')->toArray());
                return $this->success(['items' => $items]);
            } else {
                return $this->success(['items' => array_values($students->toArray())]);
            }
        }
        return $this->error('Error.', 500);
    }

    public function addStudent($class_id) {
        $email = trim(request('email'));
        $validator = Validator::make(
            ['email' => $email],
            ['email' => 'required|email|max:255']
        );
        if ($validator->fails()) {
            return $this->error($validator->messages(), 400);
        }
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            $student = Student::where('email', $email)->first();
            if (!$student) {
                $student = Student::create([
                    'name' => $email,
                    'email' => strtolower($email),
                    'password' => 'phantom',
                    'is_registered' => false
                ]);
            } else {
                $student->is_registered = true;
            }
            if ($student) {
                try {
                    $student->is_subscribed = true;
                    if (DB::table('classes_students')->where('class_id', $class_id)
                            ->where('student_id', $student->id)->count() < 1) {
                        DB::table('classes_students')->insert([
                            'class_id' => $class_id,
                            'student_id' => $student->id
                        ]);
                        $items = Application::whereHas('classes', function ($q1) use ($student, $class_id) {
                            $q1->whereHas('students', function ($q2) use ($student) {
                                $q2->where('students.id', $student->id);
                            })->where('classes.id', $class_id);
                        })->get();
                        $finished_count = 0; $past_due_count = 0;
                        foreach ($items as $item) {
                            $item->icon = $item->icon();
                            $item->is_completed = Progress::where('entity_type', 'application')->where('entity_id', $item->id)
                                    ->where('student_id', $student->id)->count() > 0;
                            $class_data = $item->getClassRelatedData($class_id);
                            if ($class_data->due_date) {
                                $due_at = $class_data->due_time ? $class_data->due_date.' '.$class_data->due_time : $class_data->due_date.' 00:00:00';
                            } else {
                                $due_at = null;
                            }
                            $item->due_at = $due_at ? Carbon::parse($due_at)->format('Y-m-d g:i A') : null;
                            $completed_at = $item->getCompletedDate($student->id);
                            $item->completed_at = $completed_at ? Carbon::parse($completed_at)->format('Y-m-d g:i A') : null;
                            $now = Carbon::now()->toDateTimeString();
                            $item->is_past_due = (!$item->is_completed && $due_at && $due_at < $now) ||
                                ($item->is_completed && $due_at && $completed_at && $due_at < $completed_at);
                            if ($item->is_completed) {
                                $finished_count++;
                            }
                            if ($item->is_past_due) {
                                $past_due_count++;
                            }
                        }
                        $student->assignments = array_values($items->sortBy('due_date')->toArray());
                        $student->assignments_finished_count = $finished_count;
                        $student->assignments_past_due_count = $past_due_count;
                        return $this->success(['item' => $student]);
                    }
                } catch (\Exception $e) {}
            }
        }
        return $this->error('Error.', 404);
    }

    public function updateStudent(Request $request, $class_id, $student_id) {
        DB::table('classes_students')
            ->where('class_id', $class_id)
            ->where('student_id', $student_id)
            ->update([
                'test_duration_multiply_by' => $request['test_duration_multiply_by'] ?: 1
            ]);
        return $this->success('updated!', 200);
    }

    public function deleteStudent($class_id, $student_id) {
        DB::table('classes_students')
            ->where('class_id', $class_id)
            ->where('student_id', $student_id)
            ->delete();
        return $this->success('deleted');
    }

    public function getReport($class_id) {
        $is_teacher = $this->user->is_teacher;
        $class_query = ClassOfStudents::query();
        $class_query->where('id', $class_id);
        if ($is_teacher) {
            $class_query->where('teacher_id', $this->user->id);
        }
        $class = $class_query->first();
        if ($class) {
            $data = $is_teacher
                ? DB::table('class_detailed_reports')
                    ->where('class_id', $class->id)
                    ->orderBy('student_email', 'ASC')
                    ->get()
                : DB::table('class_detailed_reports')
                    ->where('class_id', $class->id)
                    ->where('student_id', $this->user->id)
                    ->get();
            foreach ($data as $row) {
                $row->data = json_decode($row->data);
            }
            $assignments = $class->assignments()->orderBy('name', 'ASC')->get()->keyBy('id');
            if (!$is_teacher) {
                foreach ($assignments as $app) {
                    $class_data = $app->getClassRelatedData($class->id);
                    if ($class_data->is_for_selected_students) {
                        if (DB::table('classes_applications_students')
                                ->where('class_app_id', $class_data->id)
                                ->where('student_id', $this->user->id)->count() < 1) {
                            $assignments->forget($app->id);
                        }
                    }
                }
            }
            $tests = $class->tests()->orderBy('name', 'ASC')->get()->keyBy('id');
            foreach ($tests as $test) {
                $class_data = $test->getClassRelatedData($class->id);
                if (!$is_teacher && $class_data->is_for_selected_students) {
                    if (DB::table('classes_applications_students')
                            ->where('class_app_id', $class_data->id)
                            ->where('student_id', $this->user->id)->count() < 1) {
                        $tests->forget($test->id);
                    }
                }
                $test->icon = $test->icon();
                $test->class_id = $class_id;
                $test->app_id = $class_data && $class_data->app_id ? $class_data->app_id : 0;
                if ($class_data && $class_data->start_date) {
                    $start_at = $class_data->start_time ? $class_data->start_date.' '.$class_data->start_time : $class_data->start_date.' 00:00:00';
                } else {
                    $start_at = null;
                }
                $test->start_at = $start_at ? Carbon::parse($start_at)->format('Y-m-d g:i A') : null;
                if ($class_data && $class_data->due_date) {
                    $due_at = $class_data->due_time ? $class_data->due_date.' '.$class_data->due_time : $class_data->due_date.' 00:00:00';
                } else {
                    $due_at = null;
                }
                $test->due_at = $due_at ? Carbon::parse($due_at)->format('Y-m-d g:i A') : null;
                $test->attempts = $class_data ? $class_data->attempts : 1;
            }
            return $this->success([
                'class' => $class,
                'assignments' => array_values($assignments->toArray()),
                'tests' => array_values($tests->toArray()),
                'students' => array_values($data->toArray()),
            ]);
        }
        return $this->error('Error.', 404);
    }

    public function getAssignments($class_id) {
        $class = ClassOfStudents::where('id', $class_id)->first();
        if ($class) {
            $items = $class->assignments()->orderBy('name', 'ASC')->get()->keyBy('id');
            $students = $class->students()->get();
            $students_count = $students->count();
            foreach ($items as $item) {
                $class_data = $item->getClassRelatedData($class->id);
                if (!$this->user->is_teacher) {
                    if ($class_data->is_for_selected_students) {
                        if (DB::table('classes_applications_students')
                                ->where('class_app_id', $class_data->id)
                                ->where('student_id', $this->user->id)->count() < 1) {
                            $items->forget($item->id);
                        }
                    }
                }
                $item->icon = $item->icon();
                $item->start_date = $class_data && $class_data->start_date ? $class_data->start_date : null;
                $item->start_time = $class_data && $class_data->start_time ? $class_data->start_time : null;
                $item->due_date = $class_data && $class_data->due_date ? $class_data->due_date : null;
                $item->due_time = $class_data && $class_data->due_time ? $class_data->due_time : null;
                $item->color = $class_data && $class_data->color ? $class_data->color : null;
                $item->is_for_selected_students = $class_data && $class_data->is_for_selected_students;
                if ($item->is_for_selected_students) {
                    $item->students = DB::table('classes_applications_students')
                        ->where('class_app_id', $class_data->id)->pluck('student_id');
                    $app_students_count = count($item->students);
                } else {
                    $app_students_count = $students_count;
                }
                if ($class_data->due_date) {
                    $due_at = $class_data->due_time ? $class_data->due_date.' '.$class_data->due_time : $class_data->due_date.' 00:00:00';
                } else {
                    $due_at = null;
                }
                if ($class_data->start_date) {
                    $start_at = $class_data->start_time ? $class_data->start_date.' '.$class_data->start_time : $class_data->start_date.' 00:00:00';
                } else {
                    $start_at = null;
                }
                $now = Carbon::now()->toDateTimeString();
                $complete_count = DB::table('progresses')
                    ->where('entity_type', 'application')
                    ->where('entity_id', $item->id)
                    ->whereIn('student_id', $students->pluck('id'))
                    ->select('student_id')->distinct()->count();
                $item->progress = $app_students_count > 0 ? (round($complete_count / $app_students_count, 3)) : 1;
                if ($item->progress >= 1) {
                    $item->status = 'completed';
                } else if ($due_at && $due_at < $now) {
                    $item->status = 'overdue';
                } else if ($start_at && $start_at > $now) {
                    $item->status = 'pending';
                } else {
                    $item->status = 'progress';
                }
                if ($item->is_for_selected_students) {
                    $tracking_questions_statistics = DB::table('students_tracking_questions')->select(
                        DB::raw("SUM(1) as total"),
                        DB::raw("SUM(IF(is_right_answer, 1, 0)) as complete")
                    )->where('app_id', $item->id)->whereIn('student_id', $item->students)->first();
                } else {
                    $tracking_questions_statistics = DB::table('students_tracking_questions')->select(
                        DB::raw("SUM(1) as total"),
                        DB::raw("SUM(IF(is_right_answer, 1, 0)) as complete")
                    )->where('app_id', $item->id)->first();
                }
                $item->error_rate = 1 - ($tracking_questions_statistics->total ? $tracking_questions_statistics->complete / $tracking_questions_statistics->total : 1);
            }
            $available = Application::where('teacher_id', $this->user->id)
                ->whereNotIn('id', $items->pluck('id')->toArray())
                ->where('type', 'assignment')->orderBy('name', 'ASC')->get();
            foreach ($available as $item) {
                $item->icon = $item->icon();
            }
            return $this->success([
                'assignments' => array_values($items->sortBy('due_date')->toArray()),
                'available_assignments' => array_values($available->toArray())
            ]);
        }
        return $this->error('Error.');
    }

    public function changeAssignment($class_id, $app_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            DB::table('classes_applications')->where('class_id', $class->id)->where('app_id', $app_id)
                ->update([
                    'start_date' => request('start_date') ?: null,
                    'start_time' => request('start_time') ?: null,
                    'due_date' => request('due_date') ?: null,
                    'due_time' => request('due_time') ?: null,
                    'color' => request('color') ?: null
                ]);
            return $this->success('Ok.');
        }
        return $this->error('Error.');
    }

    public function getTests($class_id) {
        $class = ClassOfStudents::where('id', $class_id)->first();
        if ($class) {
            $items = $class->tests()->orderBy('name', 'ASC')->get()->keyBy('id');
            foreach ($items as $item) {
                $class_data = $item->getClassRelatedData($class->id);
                if (!$this->user->is_teacher) {
                    if ($class_data->is_for_selected_students) {
                        if (DB::table('classes_applications_students')
                                ->where('class_app_id', $class_data->id)
                                ->where('student_id', $this->user->id)->count() < 1) {
                            $items->forget($item->id);
                        }
                    }
                }
                $item->icon = $item->icon();
                $item->start_date = $class_data && $class_data->start_date ? $class_data->start_date : null;
                $item->start_time = $class_data && $class_data->start_time ? $class_data->start_time : null;
                $item->due_date = $class_data && $class_data->due_date ? $class_data->due_date : null;
                $item->due_time = $class_data && $class_data->due_time ? $class_data->due_time : null;
                $item->duration = $class_data && $class_data->duration ? $class_data->duration : null;
                $item->duration = round($item->duration/60); // seconds to minutes
                $item->password = $class_data && $class_data->password ? $class_data->password : null;
                $item->attempts = $class_data && $class_data->attempts ? $class_data->attempts : 1;
                $item->color = $class_data && $class_data->color ? $class_data->color : null;
                $item->is_for_selected_students = $class_data && $class_data->is_for_selected_students;
                $item->class_id = $class_id;
                $item->app_id = $class_data && $class_data->app_id ? $class_data->app_id : 0;
                if ($item->is_for_selected_students) {
                    $item->students = DB::table('classes_applications_students')
                        ->where('class_app_id', $class_data->id)->pluck('student_id');
                }
            }
            $available = Application::where('teacher_id', $this->user->id)
                ->whereNotIn('id', $items->pluck('id')->toArray())
                ->where('type', 'test')->orderBy('name', 'ASC')->get();
            foreach ($available as $item) {
                $item->icon = $item->icon();
            }
            return $this->success([
                'tests' => array_values($items->sortBy('due_date')->toArray()),
                'available_tests' => array_values($available->toArray())
            ]);
        }
        return $this->error('Error.');
    }

    public function changeTest($class_id, $app_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            DB::table('classes_applications')->where('class_id', $class->id)->where('app_id', $app_id)
                ->update([
                    'start_date' => request('start_date') ?: null,
                    'start_time' => request('start_time') ?: null,
                    'due_date' => request('due_date') ?: null,
                    'due_time' => request('due_time') ?: null,
                    'duration' => request('duration') ? (request('duration') * 60) : null, // minutes to seconds
                    'password' => request('password') ?: null,
                    'attempts' => request('attempts') ?: 1,
                    'color' => request('color') ?: null
                ]);
            return $this->success('Ok.');
        }
        return $this->error('Error.');
    }

    public function addApplicationToClass($class_id, $app_id) {
        $students = null;
        if (request()->filled('students') && request('students')) {
            $students = Student::whereIn('id', request('students'))->get();
        }
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        $exists = DB::table('classes_applications')->where('class_id', $class_id)->where('app_id', $app_id)->first();
        $app = Application::where('id', $app_id)->first();
        if (!$app) {
            return $this->error('App not found!', 404);
        }
        if ($class && !$exists) {
            DB::table('classes_applications')->insert([
                'class_id' => $class->id,
                'app_id' => $app_id,
                'start_date' => Carbon::now()->toDateString(),
                'start_time' => Carbon::now()->format('H:i'),
                'duration' => $app ? $app->duration : null,
                'is_for_selected_students' => $students ? true : false
            ]);
            $item = DB::table('classes_applications')
                ->where('class_id', $class->id)
                ->where('app_id', $app_id)
                ->first();
            if ($students) {
                foreach ($students as $student) {
                    DB::table('classes_applications_students')->insert([
                        'class_app_id' => $item->id,
                        'student_id' => $student->id,
                    ]);
                }
            }
            if ($item && $item->duration) {
                $item->duration = round($item->duration / 60); // seconds to minutes
            }
            return $this->success(['item' => $item ?: null]);
        }
        return $this->error('Error.');
    }

    public function changeApplicationStudents($class_id, $app_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if (!$class) {
            return $this->error('Class Not Found', 404);
        }
        $class_app = DB::table('classes_applications')
            ->where('class_id', $class->id)
            ->where('app_id', $app_id)->first();
        if (!$class_app) {
            return $this->error('Class Assignment Not Found', 404);
        }
        $students = null;
        if (request()->filled('students') && request('students')) {
            $students = Student::whereIn('id', request('students'))->get();
            if ($students) {
                $old_students = array_values(DB::table('classes_applications_students')
                    ->where('class_app_id', $class_app->id)->get()->pluck('student_id')->toArray());
                foreach ($students as $student) {
                    if (($key = array_search($student->id, $old_students)) !== false) {
                        unset($old_students[$key]);
                    } else {
                        DB::table('classes_applications_students')->insert([
                            'class_app_id' => $class_app->id,
                            'student_id' => $student->id,
                        ]);
                    }
                }
                foreach ($old_students as $stud_id) {
                    DB::table('classes_applications_students')
                        ->where('class_app_id', $class_app->id)
                        ->where('student_id', $stud_id)
                        ->delete();
                }
            }
        }
        return $this->success('Ok.');
    }

    public function deleteApplicationFromClass($class_id, $app_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            $item = DB::table('classes_applications')
                ->where('class_id', $class->id)
                ->where('app_id', $app_id)
                ->first();
            if ($item) {
                DB::table('classes_applications_students')->where('class_app_id', $item->id)->delete();
            }
            DB::table('classes_applications')
                ->where('class_id', $class->id)
                ->where('app_id', $app_id)
                ->delete();
            return $this->success('Ok.');
        }
        return $this->error('Error.');
    }

    public function getToDos($class_id) {
        $student = $this->user;
        $class = ClassOfStudents::where('id', $class_id)->first();
        if (!$class) {
            return $this->error('Class not found.', 404);
        }
        $items = [];
        foreach (DB::table('classes_applications')->where('class_id', $class_id)->get() as $row) {
            $item = Application::where('id', $row->app_id)->where('type', 'assignment')->first();
            if (!$item) {
                continue;
            }
            if ($item) {
                $item->class = $class;
                $item->icon = $item->icon();
                $item->is_completed = Progress::where('entity_type', 'application')->where('entity_id', $item->id)
                        ->where('student_id', $student->id)->count() > 0;
                if ($row->due_date) {
                    $due_at = $row->due_time ? $row->due_date.' '.$row->due_time : $row->due_date.' 00:00:00';
                } else {
                    $due_at = null;
                }
                $item->start_time = $row->start_time ?: '00:00:00';
                $item->start_date = $row->start_date;
                $item->due_time = $row->due_time ?: '00:00:00';
                $item->due_date = $row->due_date;
                $item->due_at = $due_at ? Carbon::parse($due_at)->format('Y-m-d g:i A') : null;
                $now = Carbon::now()->toDateTimeString();
                if ($row->start_date) {
                    $start_at = $row->start_time ? $row->start_date.' '.$row->start_time : $row->start_date.' 00:00:00';
                } else {
                    $start_at = null;
                }
                $item->is_blocked = ($start_at && $now < $start_at) || ($due_at && $now > $due_at);
                $item->start_at = $start_at && $now < $start_at ? Carbon::parse($start_at)->format('Y-m-d g:i A') : null;
                $completed_at = $item->getCompletedDate($student->id);
                $item->completed_at = $completed_at ? Carbon::parse($completed_at)->format('Y-m-d g:i A') : null;
                array_push($items, $item);
            }
        }
        return $this->success([
            'items' => array_values(collect($items)->sortBy('due_at')->toArray())
        ]);
    }

    public function getAnswersStatistics($class_id) {
        $class = ClassOfStudents::where('id', $class_id)->first();
        if (!$class) {
            return $this->error('Class not found!', 404);
        }
        $query = StudentsTrackingQuestion::query()->with('application');
        if (request()->has('app_id') && request('app_id')) {
            $query->where('app_id', request('app_id'));
        } else {
            $query->whereHas('application', function ($q1) use ($class_id) {
                $q1->with('classes')->whereHas('classes', function ($q2) use ($class_id) {
                    $q2->where('classes.id', intval($class_id));
                })->where('type', 'assignment');
            });
        }
        if (request()->has('student_id') && request('student_id')) {
            $query->where('student_id', request('student_id'));
        } else {
            $query->whereIn('student_id', $class->students()->pluck('students.id')->toArray());
        }
        if (request()->has('date_to') && request('date_to')) {
            $endDate = Carbon::parse(request('date_to'))->addDay();
            $query->where('created_at', '<', $endDate->toDateString());
        } else {
            $endDate = Carbon::now()->addDay();
        }
        if (request()->has('date_from') && request('date_from')) {
            $query->where('created_at', '>=', request('date_from'));
            $startDate = Carbon::parse(request('date_from'));
        } else {
            if (request()->has('date_to') && request('date_to')) {
                $startDate = Carbon::parse($endDate)->subDays(7);
            } else {
                $startDate = Carbon::now()->subDays(6);
            }
            $query->where('created_at', '>=', $startDate->toDateString());
        }
        $query->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(id) as attempts'),
            DB::raw('SUM(is_right_answer) as correct')
        )->groupBy(DB::raw('DATE(created_at)'));
        $rows = $query->get();
        $data = [];
        for ($i = 0; $i < $startDate->diffInDays($endDate); $i++) {
            $date = Carbon::parse($startDate->toDateString())->addDays($i)->toDateString();
            $exists = $rows->where('date', $date)->first();
            if ($exists) {
                array_push($data, (object) [
                    'date' => $date,
                    'attempts' => (int) $exists->attempts,
                    'correct' => (int) $exists->correct,
                ]);
            } else {
                array_push($data, (object) [
                    'date' => $date,
                    'attempts' => 0,
                    'correct' => 0,
                ]);
            }
        }
        return $this->success([
            'items' => $data
        ]);
    }

    public function getTestReport($class_id, $app_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        $class_app = ClassApplication::where('class_id', $class_id)->where('app_id', $app_id)->first();
        if ($class && $class_app) {
            $query = $class->students();
            $query->leftJoin('classes_applications_students', function ($join) use ($class_app) {
                $join->on('classes_applications_students.student_id', '=', 'students.id')
                    ->where('classes_applications_students.class_app_id', $class_app->id);
            });
            $query->leftJoin('students_test_attempts', function ($join) use ($class_app) {
                $join->on('students_test_attempts.test_student_id', '=', 'classes_applications_students.id');
            });
            if ($class_app->is_for_selected_students) {
                $query->whereNotNull('classes_applications_students.id');
            }
            /* using SQL JSON_ARRAYAGG function (require MySQL 5.7.22 / MariaDB 10.5.0)
            $query->groupBy('students.id', 'students.email', 'students.is_registered');
            $query->orderBy('email');
            $data = $query->get([
                'students.id',
                'students.email',
                'students.is_registered',
                DB::raw("JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'id', students_test_attempts.id,
                        'attempt_no', students_test_attempts.attempt_no,
                        'mark', students_test_attempts.mark,
                        'questions_count', students_test_attempts.questions_count,
                        'start_at', students_test_attempts.start_at,
                        'end_at', students_test_attempts.end_at
                    )
                ) AS attempts"),
                'classes_applications_students.attempts_count',
                'classes_applications_students.resets_count',
            ]);
            $data = $data->map(function ($student) {
                $attempts = json_decode($student->attempts);
                usort($attempts, function($a, $b) {return strcmp($a->attempt_no, $b->attempt_no);});
                return [
                    'id' => $student->id,
                    'email' => $student->email,
                    'is_registered' => $student->is_registered,
                    'attempts' => $attempts,
                    'attempts_count' => $student->attempts_count,
                    'resets_count' => $student->resets_count
                ];
            });
            $students = array_values($data->toArray()); */
            $query->orderBy('email');
            $data = $query->get([
                'students.id',
                'students.email',
                'students.is_registered',
                'students_test_attempts.id as attempt_id',
                'students_test_attempts.attempt_no',
                'students_test_attempts.mark',
                'students_test_attempts.questions_count',
                'students_test_attempts.start_at',
                'students_test_attempts.end_at',
                'classes_applications_students.attempts_count',
                'classes_applications_students.resets_count',
            ]);
            $students = [];
            foreach ($data as $row) {
                if (array_key_exists($row->id, $students)) {
                    array_push($students[$row->id]->attempts, (object) [
                        'id' => $row->attempt_id,
                        'attempt_no' => $row->attempt_no,
                        'mark' => $row->mark,
                        'questions_count' => $row->questions_count,
                        'start_at' => $row->start_at,
                        'end_at' => $row->end_at
                    ]);
                } else {
                    $students[$row->id] = (object) [
                        'id' => $row->id,
                        'email' => $row->email,
                        'is_registered' => $row->is_registered,
                        'attempts_count' => $row->attempts_count,
                        'resets_count' => $row->resets_count,
                        'attempts' => [
                            (object) [
                                'id' => $row->attempt_id,
                                'attempt_no' => $row->attempt_no,
                                'mark' => $row->mark,
                                'questions_count' => $row->questions_count,
                                'start_at' => $row->start_at,
                                'end_at' => $row->end_at
                            ]
                        ]
                    ];
                }
            }
            foreach ($students as $student) {
                usort($student->attempts, function($a, $b) {
                    return strcmp($a->attempt_no, $b->attempt_no);
                });
            }
            return $this->success([
                'students' => array_values($students),
            ]);
        }
        return $this->error('Error.', 500);
    }

    public function resetTestProgress(Request $request, $class_id, $app_id, $student_id) {
        $student = Student::where('id', $student_id)->first();
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        $class_app = ClassApplication::where('class_id', $class_id)->where('app_id', $app_id)->first();
        $test_student = $class_app ? $class_app->classApplicationStudents()->where('student_id', $student_id)->first() : null;
        if ($class && $class_app && $student && $test_student) {
            $attempt_id = $request->filled('attempt_id') ? intval($request['attempt_id']) : null;
            if ($attempt_id) {
                $rows_count = DB::table('students_test_attempts')
                    ->where('test_student_id', $test_student->id)
                    ->where('id', $attempt_id)
                    ->delete();
                $test_student->resets_count += $rows_count && $rows_count >= 0 ? intval($rows_count) : 1;
                $test_student->save();
                DB::table('students_test_questions')
                    ->where('class_app_id', $class_app->id)
                    ->where('attempt_id', $attempt_id)
                    ->where('student_id', $student->id)
                    ->delete();
            } else {
                $rows_count = DB::table('students_test_attempts')
                    ->where('test_student_id', $test_student->id)
                    ->delete();
                $test_student->resets_count += $rows_count && $rows_count >= 0 ? intval($rows_count) : 1;
                $test_student->save();
                DB::table('students_test_questions')
                    ->where('class_app_id', $class_app->id)
                    ->where('student_id', $student->id)
                    ->delete();
            }
            return $this->success(['success' => true], 200);
        }
        return $this->error('Error.', 500);
    }

    public function getTestDetails(Request $request, $class_id, $app_id, $student_id) {
        $student = Student::where('id', $student_id)->first();
        $class_query = ClassOfStudents::query();
        $class_query->where('id', $class_id);
        if ($this->user->isTeacher()) {
            $class_query->where('teacher_id', $this->user->id);
        } else {
            $student = $this->user;
            $student_id = $student->id;
        }
        $class = $class_query->first();
        $class_app = ClassApplication::where('class_id', $class_id)->where('app_id', $app_id)->first();
        if ($class && $student && $class_app) {
            $attempt_id = $request->filled('attempt_id') ? intval($request['attempt_id']) : null;
            if (!$attempt_id) {
                $test_student = DB::table('classes_applications_students')
                    ->where('class_app_id', $class_app->id)
                    ->where('student_id', $student_id)
                    ->first();
                if ($test_student) {
                    $attempt = DB::table('students_test_attempts')
                        ->where('test_student_id', $test_student->id)
                        ->orderBy('mark', 'DESC')
                        ->first();
                    $attempt_id = $attempt ? $attempt->id : null;
                }
            }
            return $this->success([
                'data' => $this->getTestAttemptReport($student_id, $attempt_id, false)
            ], 200);
        }
        return $this->error('Error.', 500);
    }

    public function getTestReportPDF(Request $request, $class_id, $app_id, $student_id) {
        $student = Student::where('id', $student_id)->first();
        $class_query = ClassOfStudents::query();
        $class_query->where('id', $class_id);
        if ($this->user->isTeacher()) {
            $class_query->where('teacher_id', $this->user->id);
        } else {
            $student = $this->user;
            $student_id = $student->id;
        }
        $class = $class_query->first();
        $app = Application::where('id', $app_id)->first();
        $class_app = ClassApplication::where('class_id', $class_id)->where('app_id', $app_id)->first();
        if ($class && $app && $class_app && $student) {
            $test_student = DB::table('classes_applications_students')
                ->where('class_app_id', $class_app->id)
                ->where('student_id', $student_id)
                ->first();
            if (!$test_student) { return $this->error('Error.', 500); }
            $attempt_id = $request->filled('attempt_id') ? intval($request['attempt_id']) : null;
            if ($attempt_id) {
                $attempts_rows = DB::table('students_test_attempts')
                    ->where('test_student_id', $test_student->id)
                    ->where('id', $attempt_id)
                    ->get();
            } else {
                $attempts_rows = DB::table('students_test_attempts')
                    ->where('test_student_id', $test_student->id)
                    ->orderBy('mark', 'DESC')
                    ->get();
            }
            $attempts = [];
            foreach ($attempts_rows as $attempt) {
                $attempts[] = (object) [
                    'attempt_no' => $attempt->attempt_no,
                    'mark' => $attempt->mark,
                    'questions_count' => $attempt->questions_count,
                    'start_at' => Carbon::parse($attempt->start_at)->format('M d Y g:i A'),
                    'end_at' => Carbon::parse($attempt->end_at)->format('M d Y g:i A'),
                    'levels' => $this->getTestAttemptReport($student_id, $attempt->id, true)
                ];
            }
            $pdf = PDF::loadView('exports.pdf.test_report', [
                'student' => $student,
                'test' => $app,
                'attempts' => $attempts
            ]);
            return $pdf->download('test_report.pdf');
        }
        return $this->error('Error.', 500);
    }

    private function getTestAttemptReport($student_id, $attempt_id, $with_questions = false) {
        $data = DB::table('students_test_questions')
            ->select(
                'topic_id', 'unit_id', 'level_id',
                DB::raw("SUM(1) as total"),
                DB::raw("SUM(IF(is_right_answer, 1, 0)) as correct")
            )
            ->where('attempt_id', $attempt_id)
            ->where('student_id', $student_id)
            ->groupBy('topic_id', 'unit_id', 'level_id')
            ->get();
        $levels = [];
        foreach ($data->groupBy('level_id') as $level_data) {
            $level_id = $level_data->first()->level_id;
            $level_total = $level_data->sum('total');
            $level_correct = $level_data->sum('correct');
            $units = [];
            foreach ($level_data->groupBy('unit_id') as $unit_data) {
                $unit_id = $unit_data->first()->unit_id;
                $unit_total = $unit_data->sum('total');
                $unit_correct = $unit_data->sum('correct');
                $topics = [];
                foreach ($unit_data->groupBy('topic_id') as $topic_data) {
                    $topic_id = $topic_data->first()->topic_id;
                    $topic_total = $topic_data->sum('total');
                    $topic_correct = $topic_data->sum('correct');
                    $topic = Topic::where('id', $topic_id)->first();
                    if ($with_questions) {
                        $questions = DB::table('students_test_questions')
                            ->leftJoin('question', 'question.id', '=', 'students_test_questions.question_id')
                            ->leftJoin('lesson', 'lesson.id', '=', 'question.lesson_id')
                            ->where('students_test_questions.attempt_id', $attempt_id)
                            ->where('students_test_questions.student_id', $student_id)
                            ->where('students_test_questions.topic_id', $topic_id)
                            ->orderBy('students_test_questions.order_no', 'ASC')
                            ->get([
                                'students_test_questions.question_id',
                                'question.question as question',
                                'lesson.id as lesson_id',
                                'lesson.title as lesson',
                                'students_test_questions.is_answered',
                                'students_test_questions.is_right_answer'
                            ]);
                        $topics[] = (object) [
                            'topic_id' => $topic_id,
                            'title' => $topic ? $topic->title : $topic_id,
                            'mark' => $topic_total && $topic_total > 0 ? $topic_correct / $topic_total : 1,
                            'correct' => $topic_correct,
                            'total' => $topic_total,
                            'questions' => $questions
                        ];
                    } else {
                        $topics[] = (object) [
                            'topic_id' => $topic_id,
                            'title' => $topic ? $topic->title : $topic_id,
                            'mark' => $topic_total && $topic_total > 0 ? $topic_correct / $topic_total : 1,
                            'correct' => $topic_correct,
                            'total' => $topic_total,
                        ];
                    }
                }
                $unit = Unit::where('id', $unit_id)->first();
                $units[] = (object) [
                    'unit_id' => $unit_id,
                    'title' => $unit ? $unit->title : $unit_id,
                    'mark' => $unit_total && $unit_total > 0 ? $unit_correct / $unit_total : 1,
                    'correct' => $unit_correct,
                    'total' => $unit_total,
                    'topics' => $topics
                ];
            }
            $level = Level::where('id', $level_id)->first();
            $levels[] = (object) [
                'level_id' => $level_id,
                'title' => $level ? $level->title : $level_id,
                'mark' => $level_total && $level_total > 0 ? $level_correct / $level_total : 1,
                'correct' => $level_correct,
                'total' => $level_total,
                'units' => $units
            ];
        }
        return $levels;
    }
}
