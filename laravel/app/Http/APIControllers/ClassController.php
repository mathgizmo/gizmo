<?php

namespace App\Http\APIControllers;

use App\Application;
use App\ClassOfStudents;
use App\Lesson;
use App\Progress;
use App\Student;
use App\StudentsTrackingQuestion;
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
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            $students = $class->students()->orderBy('email')
                ->get(['students.id', 'students.name', 'students.first_name', 'students.last_name', 'students.email', 'students.is_registered']);
            $apps = Application::whereHas('classes', function ($q1) use ($class_id) {
                $q1->where('classes.id', $class_id);
            })->get();
            $now = Carbon::now()->toDateTimeString();
            foreach ($students as $student) {
                $student->is_subscribed = true;
                $finished_count = 0; $past_due_count = 0;
                foreach ($apps as $app) {
                    $app->icon = $app->icon();
                    $app->is_completed = Progress::where('entity_type', 'application')->where('entity_id', $app->id)
                            ->where('student_id', $student->id)->count() > 0;
                    $class_data = $app->getClassRelatedData($class_id);
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
                $student->assignments = array_values($apps->sortBy('due_date')->toArray());
                $student->assignments_finished_count = $finished_count;
                $student->assignments_past_due_count = $past_due_count;
            }
            if ($class->subscription_type == 'invitation') {
                $emails = explode(',', strtolower(str_replace(' ', '', preg_replace( "/;|\n/", ',', $class->invitations))));
                $not_subscribed = [];
                $assignments_past_due_count = 0;
                foreach ($apps as $app) {
                    $class_data = $app->getClassRelatedData($class_id);
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
        return $this->error('Error.', 404);
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

    public function deleteStudent($class_id, $student_id) {
        DB::table('classes_students')
            ->where('class_id', $class_id)
            ->where('student_id', $student_id)
            ->delete();
        return $this->success('deleted');
    }

    public function getReport($class_id) {
        if ($this->user->is_teacher) {
            $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
            if ($class) {
                $data = DB::table('class_detailed_reports')->where('class_id', $class->id)
                    ->orderBy('student_email', 'ASC')->get();
                foreach ($data as $row) {
                    $row->data = json_decode($row->data);
                }
                return $this->success([
                    'class' => $class,
                    'assignments' => array_values($class->applications()->orderBy('name', 'ASC')->get()->toArray()),
                    'students' => array_values($data->toArray()),
                ]);
            }
        } else {
            $class = ClassOfStudents::where('id', $class_id)->first();
            if ($class) {
                $data = DB::table('class_detailed_reports')
                    ->where('class_id', $class->id)
                    ->where('student_id', $this->user->id)
                    ->get();
                foreach ($data as $row) {
                    $row->data = json_decode($row->data);
                }
                return $this->success([
                    'class' => $class,
                    'assignments' => array_values($class->applications()->orderBy('name', 'ASC')->get()->toArray()),
                    'students' => array_values($data->toArray()),
                ]);
            }
        }
        return $this->error('Error.', 404);
    }

    public function getAssignments($class_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            $items = $class->applications()->orderBy('name', 'ASC')->get();
            $students = $class->students()->get();
            $students_count = $students->count();
            foreach ($items as $item) {
                $item->icon = $item->icon();
                $class_data = $item->getClassRelatedData($class->id);
                $item->start_date = $class_data && $class_data->start_date ? $class_data->start_date : null;
                $item->start_time = $class_data && $class_data->start_time ? $class_data->start_time : null;
                $item->due_date = $class_data && $class_data->due_date ? $class_data->due_date : null;
                $item->due_time = $class_data && $class_data->due_time ? $class_data->due_time : null;
                $item->color = $class_data && $class_data->color ? $class_data->color : null;
                $class_data = $item->getClassRelatedData($class_id);
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
                $item->progress = $students_count > 0 ? (round($complete_count / $students_count, 3)) : 1;
                if ($item->progress >= 1) {
                    $item->status = 'completed';
                } else if ($due_at && $due_at < $now) {
                    $item->status = 'overdue';
                } else if ($start_at && $start_at > $now) {
                    $item->status = 'pending';
                } else {
                    $item->status = 'progress';
                }
                $tracking_questions_statistics = DB::table('students_tracking_questions')->select(
                    DB::raw("SUM(1) as total"),
                    DB::raw("SUM(IF(is_right_answer, 1, 0)) as complete")
                )->where('app_id', $item->id)->first();
                $item->error_rate = 1 - ($tracking_questions_statistics->total ? $tracking_questions_statistics->complete / $tracking_questions_statistics->total : 1);
            }
            $available = Application::where('teacher_id', $this->user->id)
                ->whereNotIn('id', $items->pluck('id')->toArray())->orderBy('name', 'ASC')->get();
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

    public function addAssignmentToClass($class_id, $app_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        $exists = DB::table('classes_applications')->where('class_id', $class_id)->where('app_id', $app_id)->first();
        if ($class && !$exists) {
            DB::table('classes_applications')->insert([
                'class_id' => $class->id,
                'app_id' => $app_id,
                'start_date' => Carbon::now()->toDateString(),
                'start_time' => Carbon::now()->format('H:i')
            ]);
            $item = DB::table('classes_applications')->where('class_id', $class->id)->where('app_id', $app_id)->first();
            return $this->success(['item' => $item ?: null]);
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

    public function deleteAssignmentFromClass($class_id, $app_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            DB::table('classes_applications')->where('class_id', $class->id)->where('app_id', $app_id)
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
            $item = Application::where('id', $row->app_id)->first();
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

    public function geAnswersStatistics($class_id) {
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
                });
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
        $rows = $query->get();
        $data = [];
        for ($i = 0; $i < $startDate->diffInDays($endDate); $i++) {
            $date = Carbon::parse($startDate->toDateString())->addDays($i)->toDateString();
            $attempts = 0;
            $correct = 0;
            foreach ($rows->where('created_at', '>=', $date)
                         ->where('created_at', '<', Carbon::parse($date)->addDays(1)->toDateString()) as $row) {
                $attempts++;
                if ($row->is_right_answer) {
                    $correct++;
                }
            }
            array_push($data, (object) [
                'date' => $date,
                'attempts' => $attempts,
                'correct' => $correct,
            ]);
        }
        return $this->success([
            'items' => $data
        ]);
    }
}