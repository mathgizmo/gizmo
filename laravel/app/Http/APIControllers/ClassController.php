<?php

namespace App\Http\APIControllers;

use App\Application;
use App\ClassOfStudents;
use App\Progress;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
            return $this->success('Ok.');
        }
        return $this->error('Error.', 404);
    }

    public function getStudents($class_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            $students = $class->students()->orderBy('name')->get(['students.id', 'students.name', 'students.first_name', 'students.last_name', 'students.email']);
            foreach ($students as $student) {
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
            }
            return $this->success(['items' => array_values($students->toArray())]);
        }
        return $this->error('Error.', 404);
    }

    public function getReport($class_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            $students = $class->students()->orderBy('name')
                ->get(['students.id', 'students.name', 'students.first_name', 'students.last_name', 'students.email']);
            $apps = $class->applications()->get();
            foreach ($students as $student) {
                $assignments = [];
                foreach ($apps as $app) {
                    $is_completed = Progress::where('entity_type', 'application')->where('entity_id', $app->id)
                            ->where('student_id', $student->id)->count() > 0;
                    $class_data = $app->getClassRelatedData($class_id);
                    if ($class_data->due_date) {
                        $due_at = $class_data->due_time ? $class_data->due_date.' '.$class_data->due_time : $class_data->due_date.' 00:00:00';
                    } else {
                        $due_at = null;
                    }
                    $app->due_at = $due_at ? Carbon::parse($due_at)->format('Y-m-d g:i A') : null;
                    $completed_at = $app->getCompletedDate($student->id);
                    $now = Carbon::now()->toDateTimeString();
                    $is_past_due = (!$is_completed && $due_at && $due_at < $now) ||
                        ($is_completed && $due_at && $completed_at && $due_at < $completed_at);
                    if ($is_completed) {
                        $assignments[$app->id] = 'completed';
                    } else if ($is_past_due) {
                        $assignments[$app->id] = 'overdue';
                    } else {
                        $assignments[$app->id] = 'progress';
                    }
                }
                $student->assignments = $assignments;
            }
            return $this->success([
                'class' => $class,
                'students' => array_values($students->toArray()),
                'assignments' => array_values($apps->toArray()),
            ]);
        }
        return $this->error('Error.', 404);
    }

    public function getAssignments($class_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            $items = $class->applications()->get();
            foreach ($items as $item) {
                $item->icon = $item->icon();
                $class_data = $item->getClassRelatedData($class->id);
                $item->start_date = $class_data && $class_data->start_date ? $class_data->start_date : null;
                $item->start_time = $class_data && $class_data->start_time ? $class_data->start_time : null;
                $item->due_date = $class_data && $class_data->due_date ? $class_data->due_date : null;
                $item->due_time = $class_data && $class_data->due_time ? $class_data->due_time : null;
            }
            $available = Application::where('teacher_id', $this->user->id)
                ->whereNotIn('id', $items->pluck('id')->toArray())->orderBy('name')->get();
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
                'app_id' => $app_id
            ]);
            return $this->success('Ok.');
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
                    'due_time' => request('due_time') ?: null
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
        return $this->success([
            'items' => array_values(collect($items)->sortBy('due_at')->toArray())
        ]);
    }
}
