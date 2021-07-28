<?php

namespace App\Http\APIControllers;

use App\ClassApplicationStudent;
use App\ClassStudent;
use App\Exports\ClassAssignmentsReportExport;
use App\Exports\ClassTestsReportExport;
use App\Mail\ClassMail;
use App\StudentTestAttempt;
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
use Illuminate\Support\Facades\Mail;
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
        $user_id = $this->user->id;
        $items = ClassOfStudents::where(function ($q1) use($user_id) {
            $q1->where('classes.teacher_id', $user_id)
                ->orWhereHas('teachers', function ($q2) use($user_id) {
                    $q2->where('students.id', $user_id);
                });
        })->get();
        return $this->success([
            'items' => array_values($items->toArray())
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
                    'invitations' => request('invitations'),
                    'is_researchable' => request('is_researchable')
                ])
            ]);
        } catch (\Exception $e) {
            return $this->error('Error.', 404);
        }
    }

    public function update($class_id) {
        try {
            $user_id = $this->user->id;
            $class = ClassOfStudents::where('id', $class_id)
                ->where(function ($q1) use($user_id) {
                    $q1->where('classes.teacher_id', $user_id)
                        ->orWhereHas('teachers', function ($q2) use($user_id) {
                            $q2->where('students.id', $user_id);
                        });
                })->first();
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
                if (request()->has('is_researchable')) {
                    $class->is_researchable = request('is_researchable');
                }
                $class->save();
                return $this->success(['item' => $class]);
            }
        } catch (\Exception $e) {}
        return $this->error('Error.', 404);
    }

    public function delete($class_id) {
        if ($class_id == 1) {
            abort('400', 'Default class can\'t be deleted!');
        }
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            $class->delete();
            DB::table('classes_applications')->where('class_id', $class_id)->delete();
            return $this->success('Ok.');
        }
        return $this->error('Error.', 404);
    }

    public function emailClass(Request $request, $class_id) {
        $user = $this->user;
        $class = ClassOfStudents::where('id', $class_id)->first();
        if (!$class) { return $this->error('Class not found!', 404); }
        if ($user->isTeacher()) {
            $emails = request('for_all_students')
                ? array_values($class->students->pluck('email')->toArray())
                : request('students');
        } else {
            if (request('for_all_teachers')) {
                $teachers = $class->teachers()
                    ->where('receive_emails_from_students', true)
                    ->pluck('email')->toArray();
                $emails = array_merge([$class->teacher->email], array_values($teachers));
            } else {
                $emails = request('teachers');
            }
        }
        if (config('app.env') == 'production') {
            foreach ($emails as $email) {
                try {
                    Mail::to($email)->send(new ClassMail($request['subject'], $request['body'], $user, $class));
                } catch (\Exception $e) { return $e;}
            }
        }
        return $this->success(['success' => true, 'items' => $emails]);
    }

    public function getStudents($class_id) {
        $show_extra = request()->filled('extra') && request('extra') == 'true' ? true : false;
        $user_id = $this->user->id;
        $class = ClassOfStudents::where('id', $class_id)
            ->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            })->first();
        if ($class) {
            $students = $class->students()->orderBy('email')
                ->get([
                    'students.id',
                    'students.first_name',
                    'students.last_name',
                    'students.email',
                    'students.is_registered',
                    'students.created_at',
                ]);
            if ($show_extra) {
                $apps = Application::whereHas('classes', function ($q1) use ($class_id) {
                    $q1->where('classes.id', $class_id);
                })->where('type', 'assignment')->get();
                foreach ($students as $student) {
                    $class_student = DB::table('classes_students')
                        ->where('class_id', $class_id)
                        ->where('student_id', $student->id)
                        ->first();
                    $student->test_duration_multiply_by = $class_student ? $class_student->test_duration_multiply_by : 1;
                    $student->is_subscribed = true;
                    $student->is_unsubscribed = $class_student ? $class_student->is_unsubscribed : false;
                    $assignments_finished_count = 0;
                    foreach ($apps as $app) {
                        $class_data = $app->getClassRelatedData($class_id);
                        if ($class_data->is_for_selected_students) {
                            if (DB::table('classes_applications_students')
                                    ->where('class_app_id', $class_data->id)
                                    ->where('student_id', $student->id)->count() < 1) {
                                continue;
                            }
                        }
                        $app->is_completed = Progress::where('entity_type', 'application')->where('entity_id', $app->id)
                                ->where('student_id', $student->id)->count() > 0;
                        if ($app->is_completed) {
                            $assignments_finished_count++;
                        }
                    }
                    $student->assignments_finished_count = $assignments_finished_count;
                    $tests_finished_count = ClassApplication::where('class_id', $class_id)
                        ->whereHas('classApplicationStudents', function ($q) use ($student) {
                            $q->where('student_id', $student->id)
                                ->whereHas('testAttempts', function ($q) {
                                    $q->whereNotNull('end_at');
                                });
                        })
                        ->select('classes_applications.id')->distinct()->count();
                    $student->tests_finished_count = $tests_finished_count;
                }
                if ($class->subscription_type == 'assigned') {
                    $emails = explode(',', strtolower(str_replace(' ', '', preg_replace( "/;|\n/", ',', $class->invitations))));
                    $not_subscribed = [];
                    foreach (array_filter($emails) as $email) {
                        $email = str_replace('"', '', trim($email));
                        if ($students->where('email', $email)->count() < 1) {
                            $student = Student::where('email', $email)->first();
                            if ($student) {
                                array_push($not_subscribed, (object) [
                                    'id' => $student->id,
                                    'first_name' => $student->first_name,
                                    'last_name' => $student->last_name,
                                    'email' => $email,
                                    'is_registered' => $student->is_registered,
                                    'is_subscribed' => false,
                                    'assignments_finished_count' => 0,
                                    'tests_finished_count' => 0,
                                    'created_at' => $student->created_at
                                ]);
                            } else {
                                array_push($not_subscribed, (object) [
                                    'id' => null,
                                    'first_name' => null,
                                    'last_name' => null,
                                    'email' => $email,
                                    'is_registered' => false,
                                    'is_subscribed' => false,
                                    'assignments_finished_count' => 0,
                                    'tests_finished_count' => 0,
                                    'created_at' => null
                                ]);
                            }
                        }
                    }
                    $not_sorted_items = array_merge(array_values($students->toArray()), $not_subscribed);
                    $items = array_values(collect($not_sorted_items)->sortBy('email')->toArray());
                    return $this->success(['items' => $items]);
                }
            }
            return $this->success(['items' => array_values($students->toArray())]);
        }
        return $this->error('Error.', 500);
    }

    public function addStudents(Request $request, $class_id) {
        $user_id = $this->user->id;
        $request_emails = request('email');
        if (!$request_emails) {
            return $this->error('Email is required', 400);
        }
        $emails = explode(',', strtolower(str_replace(' ', '', preg_replace( "/;|\n/", ',', $request_emails))));
        $class = ClassOfStudents::where('id', $class_id)
            ->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            })->first();
        if (!$class) { return $this->error('Class not found.', 404); }
        $students = [];
        foreach ($emails as $email) {
            $email = str_replace('"', '', trim($email));
            $validator = Validator::make(
                ['email' => $email],
                ['email' => 'required|email|max:255']
            );
            if ($validator->fails()) { continue; }
            try {
                $student = Student::where('email', $email)->first();
                if (!$student) {
                    $student = Student::create([
                        'email' => strtolower($email),
                        'password' => 'phantom',
                        'is_registered' => false
                    ]);
                } else {
                    $student->is_registered = true;
                }
                $student->is_subscribed = true;
                $exists = DB::table('classes_students')
                    ->where('class_id', $class_id)
                    ->where('student_id', $student->id)
                    ->first();
                if (!$exists || $exists->is_unsubscribed) {
                    if ($exists && $exists->is_unsubscribed) {
                        DB::table('classes_students')
                            ->where('class_id', $class_id)
                            ->where('student_id', $student->id)
                            ->update([
                                'is_unsubscribed' => false
                            ]);
                    } else {
                        DB::table('classes_students')->insert([
                            'class_id' => $class_id,
                            'student_id' => $student->id
                        ]);
                    }
                    $assignments = Application::whereHas('classes', function ($q1) use ($student, $class_id) {
                        $q1->whereHas('students', function ($q2) use ($student) {
                            $q2->where('students.id', $student->id);
                        })->where('classes.id', $class_id);
                    })->get();
                    $assignments_finished_count = 0;
                    foreach ($assignments as $item) {
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
                            $assignments_finished_count++;
                        }
                    }
                    $student->assignments = array_values($assignments->sortBy('due_date')->toArray());
                    $student->assignments_finished_count = $assignments_finished_count;
                    $tests_finished_count = ClassApplication::where('class_id', $class_id)
                        ->whereHas('classApplicationStudents', function ($q) use ($student) {
                            $q->where('student_id', $student->id)
                                ->whereHas('testAttempts', function ($q) {
                                    $q->whereNotNull('end_at');
                                });
                        })
                        ->select('classes_applications.id')->distinct()->count();
                    $student->tests_finished_count = $tests_finished_count;
                    array_push($students, $student);
                }
            } catch (\Exception $e) { }
        }
        return $this->success(['items' => $students]);
    }

    public function updateStudent(Request $request, $class_id, $student_id) {
        $user_id = $this->user->id;
        $class = ClassOfStudents::where('id', $class_id)
            ->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            })->first();
        if (!$class) { return $this->error('Class not found.', 404); }
        DB::table('classes_students')
            ->where('class_id', $class_id)
            ->where('student_id', $student_id)
            ->update([
                'test_duration_multiply_by' => $request['test_duration_multiply_by'] ?: 1
            ]);
        return $this->success('updated!', 200);
    }

    public function deleteStudent($class_id, $student_id) {
        $user_id = $this->user->id;
        $class = ClassOfStudents::where('id', $class_id)
            ->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            })->first();
        if (!$class) { return $this->error('Class not found.', 404); }
        ClassStudent::where('class_id', $class_id)->where('student_id', $student_id)->delete();
        return $this->success(['success' => true]);
    }

    public function addStudent($class_id, $student_id) {
        $user_id = $this->user->id;
        $class = ClassOfStudents::where('id', $class_id)
            ->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            })->first();
        if (!$class) { return $this->error('Class not found.', 404); }

        $exists = ClassStudent::where('class_id', $class_id)
            ->where('student_id', $student_id)
            ->first();
        if ($exists) {
            $exists->is_unsubscribed = false;
            $exists->save();
        } else {
            DB::table('classes_students')->insert([
                'class_id' => $class_id,
                'student_id' => $student_id
            ]);
        }
        return $this->success(['success' => true]);
    }

    public function getStudentAssignmentsReport(Request $request, $class_id, $student_id)
    {
        $user_id = $this->user->id;
        $class = ClassOfStudents::where('id', $class_id)
            ->where(function ($q1) use ($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use ($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            })->first();
        if (!$class) { return $this->error('Class not found.', 404); }
        $now = Carbon::now()->toDateTimeString();
        $apps = Application::whereHas('classes', function ($q1) use ($class_id) {
            $q1->where('classes.id', $class_id);
        })->where('type', 'assignment')->get();
        $student_assignments = $apps->keyBy('id');
        foreach ($apps as $app) {
            $class_data = $app->getClassRelatedData($class_id);
            if ($class_data->is_for_selected_students) {
                if (DB::table('classes_applications_students')
                        ->where('class_app_id', $class_data->id)
                        ->where('student_id', $student_id)->count() < 1) {
                    $student_assignments->forget($app->id);
                    continue;
                }
            }
            $app->icon = $app->icon();
            $app->lessons_count = $app->getLessonsQuery()->count();
            $app->lessons_complete = Progress::where('entity_type', 'lesson')->where('app_id', $app->id)
                ->where('student_id', $student_id)->count();
            $class_tracking_questions_statistics = DB::table('students_tracking_questions')
                ->select(
                    DB::raw("SUM(1) as total"),
                    DB::raw("SUM(IF(is_right_answer, 1, 0)) as correct")
                )
                ->where('class_app_id', $class_data->id)
                ->where('student_id', $student_id)
                ->first();
            $app->questions_correct = $class_tracking_questions_statistics && $class_tracking_questions_statistics->correct ? $class_tracking_questions_statistics->correct : 0;
            $app->questions_attempted = $class_tracking_questions_statistics && $class_tracking_questions_statistics->total ? $class_tracking_questions_statistics->total : 0;
            $app->correct_rate = $app->questions_attempted > 0 ? $app->questions_correct / $app->questions_attempted : 1;
            $app->is_completed = Progress::where('entity_type', 'application')->where('entity_id', $app->id)
                    ->where('student_id', $student_id)->count() > 0;
             if ($class_data->due_date) {
                 $due_at = $class_data->due_time ? $class_data->due_date.' '.$class_data->due_time : $class_data->due_date.' 00:00:00';
             } else {
                 $due_at = null;
             }
             $app->due_at = $due_at ? Carbon::parse($due_at)->format('Y-m-d g:i A') : null;
             $completed_at = $app->getCompletedDate($student_id);
             $app->completed_at = $completed_at ? Carbon::parse($completed_at)->format('Y-m-d g:i A') : null;
             $app->is_past_due = (!$app->is_completed && $due_at && $due_at < $now) || ($app->is_completed && $due_at && $completed_at && $due_at < $completed_at);
        }
        return $this->success(['items' => array_values($student_assignments->sortBy('due_date')->toArray())]);
    }

    public function getStudentTestsReport(Request $request, $class_id, $student_id)
    {
        $user_id = $this->user->id;
        $class = ClassOfStudents::where('id', $class_id)
            ->where(function ($q1) use ($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use ($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            })->first();
        if (!$class) { return $this->error('Class not found.', 404); }
        $now = Carbon::now()->toDateTimeString();
        $apps = Application::whereHas('classes', function ($q1) use ($class_id) {
            $q1->where('classes.id', $class_id);
        })->where('type', 'test')->get();
        $student_tests = $apps->keyBy('id');
        foreach ($apps as $app) {
            $app_id = $app->id;
            $class_data = $app->getClassRelatedData($class_id);
            if ($class_data->is_for_selected_students) {
                if (DB::table('classes_applications_students')
                        ->where('class_app_id', $class_data->id)
                        ->where('student_id', $student_id)->count() < 1) {
                    $student_tests->forget($app_id);
                    continue;
                }
            }
            $app->icon = $app->icon();
            $attempt = StudentTestAttempt::whereHas('testStudent', function ($q1) use($student_id, $class_id, $app_id) {
                $q1->where('student_id', $student_id)
                    ->whereHas('classApplication', function ($q2) use ($class_id, $app_id) {
                        $q2->where('class_id', $class_id)->where('app_id', $app_id);
                    });
            })->orderBy('mark', 'DESC')->first();
            $app->is_completed = $attempt && $attempt->mark;
            $app->mark = $app->is_completed ? $attempt->mark : null;
            $app->questions_count = $attempt ? $attempt->questions_count : null;
            if ($class_data->due_date) {
                $due_at = $class_data->due_time ? $class_data->due_date.' '.$class_data->due_time : $class_data->due_date.' 00:00:00';
            } else {
                $due_at = null;
            }
            $app->due_at = $due_at ? Carbon::parse($due_at)->format('Y-m-d g:i A') : null;
            $completed_at = $app->is_completed ? $attempt->end_at : null;
            $app->completed_at = $completed_at ? Carbon::parse($completed_at)->format('Y-m-d g:i A') : null;
            $app->is_past_due = (!$app->is_completed && $due_at && $due_at < $now) || ($app->is_completed && $due_at && $completed_at && $due_at < $completed_at);
        }
        return $this->success(['items' => array_values($student_tests->sortBy('due_date')->toArray())]);
    }

    public function getTeachers(Request $request, $class_id) {
        $class = ClassOfStudents::where('id', $class_id)->first();
        if (!$class) {
            return $this->error('Classroom not found!', 404);
        }
        $is_researcher = $request->filled('is_researcher') && $request['is_researcher'];
        $items = $class->teachers()
            ->whereHas('classTeachers', function ($q) use ($is_researcher) {
                $q->where('classes_teachers.is_researcher', $is_researcher);
            })
            ->orderBy('email', 'ASC')
            ->get()->keyBy('id');
        if ($is_researcher) {
            $teachers = array_values($items->toArray());
        } else {
            foreach ($items as $item) {
                $class_data = DB::table('classes_teachers')
                    ->where('class_id', $class_id)
                    ->where('student_id', $item->id)
                    ->first();
                $item->receive_emails_from_students = $class_data->receive_emails_from_students;
                if ($request->filled('receive_emails_from_students') && $request['receive_emails_from_students'] && !$item->receive_emails_from_students) {
                    $items->forget($item->id);
                }
            }
            $main_teacher = $class->teacher;
            $main_teacher->receive_emails_from_students = 1;
            $teachers = array_merge([$main_teacher], array_values($items->toArray()));
        }
        $not_available = $class->teachers()->pluck('students.id')->toArray();
        $available = Student::where('is_teacher', true)
            ->when($is_researcher, function($query) {
                return $query->where('is_researcher', true);
            })
            ->whereNotIn('students.id', $not_available)
            ->where('id', '<>', $this->user->id)
            ->orderBy('email', 'ASC')->get();
        $available_teachers = array_values($available->toArray());
        return $this->success([
            'teachers' => $teachers,
            'available_teachers' => $available_teachers
        ]);
    }

    public function addTeacherToClass(Request $request, $class_id, $teacher_id) {
        $class = ClassOfStudents::where('id', $class_id)
            ->where('teacher_id', $this->user->id
            )->first();
        if (!$class) {
            return $this->error('Classroom not found!', 404);
        }
        $teacher = Student::where('id', $teacher_id)->where('is_teacher', true)->first();
        if (!$teacher) {
            return $this->error('Teacher not found!', 404);
        }
        $exists = DB::table('classes_teachers')
            ->where('class_id', $class_id)
            ->where('student_id', $teacher_id)
            ->first();
        if (!$exists) {
            DB::table('classes_teachers')->insert([
                'class_id' => $class->id,
                'student_id' => $teacher_id,
                'is_researcher' => $request->filled('is_researcher') ? $request['is_researcher'] : false
            ]);
            $item = DB::table('classes_teachers')
                ->where('class_id', $class->id)
                ->where('student_id', $teacher_id)
                ->first();
            return $this->success(['item' => $item ?: null]);
        }
        return $this->error('Error.');
    }

    public function updateTeacher(Request $request, $class_id, $teacher_id) {
        $user_id = $this->user->id;
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $user_id)->first();
        if (!$class) { return $this->error('Class not found.', 404); }
        DB::table('classes_teachers')
            ->where('class_id', $class_id)
            ->where('student_id', $teacher_id)
            ->update([
                'receive_emails_from_students' => $request['receive_emails_from_students'] ? true : false
            ]);
        return $this->success('updated!', 200);
    }

    public function deleteTeacherFromClass($class_id, $teacher_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            DB::table('classes_teachers')
                ->where('class_id', $class->id)
                ->where('student_id', $teacher_id)
                ->delete();
            return $this->success('Ok.');
        }
        return $this->error('Error.');
    }

    public function getReport(Request $request, $class_id) {
        $data = $this->getReportData($request, $class_id, true);
        if ($data) {
            return $this->success($data);
        } else {
            return $this->error('Error.', 404);
        }
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
                $item->students_count = $app_students_count;
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
                    )->where('app_id', $item->id)
                        ->whereIn('student_id', $item->students)
                        ->first();
                    $class_tracking_questions_statistics = DB::table('students_tracking_questions')
                        ->select(
                            DB::raw("SUM(1) as total"),
                            DB::raw("SUM(IF(is_right_answer, 1, 0)) as complete")
                        )
                        ->where('class_app_id', $class_data->id)
                        ->whereIn('student_id', $item->students)
                        ->first();
                } else {
                    $tracking_questions_statistics = DB::table('students_tracking_questions')->select(
                        DB::raw("SUM(1) as total"),
                        DB::raw("SUM(IF(is_right_answer, 1, 0)) as complete")
                    )->where('app_id', $item->id)->first();
                    $class_tracking_questions_statistics = DB::table('students_tracking_questions')
                        ->select(
                            DB::raw("SUM(1) as total"),
                            DB::raw("SUM(IF(is_right_answer, 1, 0)) as complete")
                        )
                        ->where('class_app_id', $class_data->id)
                        ->whereIn('student_id', array_values($students->pluck('id')->toArray()))
                        ->first();
                }
                $item->error_rate = 1 - ($tracking_questions_statistics->total ? $tracking_questions_statistics->complete / $tracking_questions_statistics->total : 1);
                $item->class_error_rate = 1 - ($class_tracking_questions_statistics->total ? $class_tracking_questions_statistics->complete / $class_tracking_questions_statistics->total : 1);
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
        $user_id = $this->user->id;
        $class = ClassOfStudents::where('id', $class_id)
            ->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            })->first();
        if (!$class) { return $this->error('Class not found.', 404); }
        DB::table('classes_applications')
            ->where('class_id', $class->id)
            ->where('app_id', $app_id)
            ->update([
                'start_date' => request('start_date') ?: null,
                'start_time' => request('start_time') ?: null,
                'due_date' => request('due_date') ?: null,
                'due_time' => request('due_time') ?: null,
                'color' => request('color') ?: null
            ]);
        return $this->success('Ok.');
    }

    public function getTests($class_id) {
        $class = ClassOfStudents::where('id', $class_id)->first();
        if ($class) {
            $items = $class->tests()->orderBy('name', 'ASC')->get()->keyBy('id');
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
                    $app_students_count = count($item->students);
                } else {
                    $app_students_count = $students_count;
                }
                $item->students_count = $app_students_count;
                $complete_count = ClassApplicationStudent::where('class_app_id', $class_data->id)
                    ->whereIn('student_id', $students->pluck('id'))
                    ->whereHas('testAttempts', function ($q) {
                        $q->whereNotNull('end_at');
                    })
                    ->select('classes_applications_students.id')
                    ->distinct()->count();
                $item->progress = $app_students_count > 0 ? (round($complete_count / $app_students_count, 3)) : 1;
                if ($item->is_for_selected_students) {
                    $tracking_questions_statistics = DB::table('students_tracking_questions')
                        ->select(
                            DB::raw("SUM(1) as total"),
                            DB::raw("SUM(IF(is_right_answer, 1, 0)) as complete")
                        )
                        ->where('class_app_id', $class_data->id)
                        ->whereIn('student_id', $item->students)
                        ->first();
                } else {
                    $tracking_questions_statistics = DB::table('students_tracking_questions')
                        ->select(
                            DB::raw("SUM(1) as total"),
                            DB::raw("SUM(IF(is_right_answer, 1, 0)) as complete")
                        )
                        ->where('class_app_id', $class_data->id)
                        ->whereIn('student_id', array_values($students->pluck('id')->toArray()))
                        ->first();
                }
                $item->error_rate = 1 - ($tracking_questions_statistics->total ? $tracking_questions_statistics->complete / $tracking_questions_statistics->total : 1);
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
        $user_id = $this->user->id;
        $class = ClassOfStudents::where('id', $class_id)
            ->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            })->first();
        if (!$class) { return $this->error('Class not found.', 404); }
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

    public function addApplicationToClass($class_id, $app_id) {
        $students = null;
        if (request()->filled('students') && request('students')) {
            $students = Student::whereIn('id', request('students'))->get();
        }
        $user_id = $this->user->id;
        $class = ClassOfStudents::where('id', $class_id)
            ->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            })->first();
        if (!$class) { return $this->error('Class not found.', 404); }
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
        $user_id = $this->user->id;
        $class = ClassOfStudents::where('id', $class_id)
            ->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            })->first();
        if (!$class) { return $this->error('Class not found.', 404); }
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
        $user_id = $this->user->id;
        $class = ClassOfStudents::where('id', $class_id)
            ->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            })->first();
        if (!$class) { return $this->error('Class not found.', 404); }
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

    public function getAnswersStatistics($class_id) {
        $class = ClassOfStudents::where('id', $class_id)->first();
        if (!$class) { return $this->error('Class not found!', 404); }
        $type = request()->has('type') ? request('type') : 'assignment';
        $query = StudentsTrackingQuestion::query()->with('application');
        $query->whereHas('classApplication', function ($q1) use ($class_id) {
            $q1->where('class_id', $class_id);
        });
        if (request()->has('app_id') && request('app_id')) {
            $query->where('app_id', request('app_id'));
        } else {
            $query->whereHas('application', function ($q1) use ($type) {
                $q1->where('type', $type);
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
        $user_id = $this->user->id;
        $class = ClassOfStudents::where('id', $class_id)
            ->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            })->first();
        if (!$class) { return $this->error('Class not found.', 404); }
        $class_app = ClassApplication::where('class_id', $class_id)
            ->where('app_id', $app_id)->first();
        if (!$class_app) {
            return $this->error('Test not found!', 404);
        }
        $students = $this->getTestReportData($class, $class_app);
        return $this->success([
            'students' => array_values($students),
        ]);
    }

    public function resetTestProgress(Request $request, $class_id, $app_id, $student_id) {
        $student = Student::where('id', $student_id)->first();
        $user_id = $this->user->id;
        $class = ClassOfStudents::where('id', $class_id)
            ->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            })->first();
        if (!$class) { return $this->error('Class not found.', 404); }
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
            $user_id = $this->user->id;
            $class_query->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            });
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

    public function downloadTestReportPDF(Request $request, $class_id, $app_id, $student_id) {
        $student = Student::where('id', $student_id)->first();
        $class_query = ClassOfStudents::query();
        $class_query->where('id', $class_id);
        if ($this->user->isTeacher()) {
            $user_id = $this->user->id;
            $class_query->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            });
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
                    ->orderBy('attempt_no', 'ASC')
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

    /* public function downloadTestPoorQuestionsReportPDF(Request $request, $class_id, $app_id) {
        $class_query = ClassOfStudents::query();
        $class_query->where('id', $class_id);
        $user_id = $this->user->id;
        $class_query->where(function ($q1) use($user_id) {
            $q1->where('classes.teacher_id', $user_id)
                ->orWhereHas('teachers', function ($q2) use($user_id) {
                    $q2->where('students.id', $user_id);
                });
        });
        $class = $class_query->first();
        $app = Application::where('id', $app_id)->first();
        $class_app = ClassApplication::where('class_id', $class_id)->where('app_id', $app_id)->first();
        if ($class && $app && $class_app) {
            $questions = [];
            $pdf = PDF::loadView('exports.pdf.test_poor_questions_report', [
                'test' => $app,
                'questions' => $questions,
            ]);
            return $pdf->download('test_poor_questions_report.pdf');
        }
        return $this->error('Error.', 500);
    } */

    public function downloadTestsReport(Request $request, $class_id, $format = 'csv') {
        $user_id = $this->user->id;
        $class = ClassOfStudents::where('id', $class_id)
            ->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            })->first();
        if (!$class) { return $this->error('Class not found!', 404); }
        $students = $class->students()->orderBy('email', 'ASC')->get(['email']);
        $tests = [];
        foreach ($class->classApplications()->whereHas('test')->orderBy('start_date', 'ASC')->get() as $class_app) {
            $test = $class_app->test()->first('name');
            $test->attempts = $class_app->attempts ?: 1;
            $test->students = $this->getTestReportData($class, $class_app);
            array_push($tests, $test);
        }
        switch ($format) {
            case 'xls':
                return (new ClassTestsReportExport($class, $students, $tests))
                    ->download('tests_report.xls', \Maatwebsite\Excel\Excel::XLS);
            case 'xlsx':
                return (new ClassTestsReportExport($class, $students, $tests))
                    ->download('tests_report.xlsx', \Maatwebsite\Excel\Excel::XLSX);
            case 'tsv':
                return (new ClassTestsReportExport($class, $students, $tests))
                    ->download('tests_report.tsv', \Maatwebsite\Excel\Excel::TSV);
            case 'ods':
                return (new ClassTestsReportExport($class, $students, $tests))
                    ->download('tests_report.ods', \Maatwebsite\Excel\Excel::ODS);
            case 'html':
                return (new ClassTestsReportExport($class, $students, $tests))
                    ->download('tests_report.html', \Maatwebsite\Excel\Excel::HTML);
            /** PDF export require extra library: https://phpspreadsheet.readthedocs.io/en/latest/topics/reading-and-writing-to-file/#pdf
            case 'pdf':
                return (new ClassTestsReportExport($class, $students, $tests))
                    ->download('tests_report.pdf', \Maatwebsite\Excel\Excel::MPDF/DOMPDF/TCPDF); */
            default:
                return (new ClassTestsReportExport($class, $students, $tests))
                    ->download('tests_report.csv', \Maatwebsite\Excel\Excel::CSV, [
                        'Content-Type' => 'text/csv',
                    ]);
        }
    }

    public function downloadAssignmentsReport(Request $request, $class_id, $format = 'csv') {
        $data = $this->getReportData($request, $class_id, false);
        if (!$data) {
            return $this->error('Error.', 404);
        }
        switch ($format) {
            case 'xls':
                return (new ClassAssignmentsReportExport($data))
                    ->download('assignments_report.xls', \Maatwebsite\Excel\Excel::XLS);
            case 'xlsx':
                return (new ClassAssignmentsReportExport($data))
                    ->download('assignments_report.xlsx', \Maatwebsite\Excel\Excel::XLSX);
            case 'tsv':
                return (new ClassAssignmentsReportExport($data))
                    ->download('assignments_report.tsv', \Maatwebsite\Excel\Excel::TSV);
            case 'ods':
                return (new ClassAssignmentsReportExport($data))
                    ->download('assignments_report.ods', \Maatwebsite\Excel\Excel::ODS);
            case 'html':
                return (new ClassAssignmentsReportExport($data))
                    ->download('assignments_report.html', \Maatwebsite\Excel\Excel::HTML);
            /** PDF export require extra library: https://phpspreadsheet.readthedocs.io/en/latest/topics/reading-and-writing-to-file/#pdf
            case 'pdf':
                return (new ClassAssignmentsReportExport($data))
                    ->download('assignments_report.pdf', \Maatwebsite\Excel\Excel::MPDF/DOMPDF/TCPDF); */
            default:
                return (new ClassAssignmentsReportExport($data))
                    ->download('assignments_report.csv', \Maatwebsite\Excel\Excel::CSV, [
                        'Content-Type' => 'text/csv',
                    ]);
        }
    }

    private function getReportData(Request $request, $class_id, $with_tests = true) {
        $is_teacher = $this->user->is_teacher;
        $class_query = ClassOfStudents::query();
        $class_query->where('id', $class_id);
        if ($is_teacher) {
            $user_id = $this->user->id;
            $class_query->where(function ($q1) use($user_id) {
                $q1->where('classes.teacher_id', $user_id)
                    ->orWhereHas('teachers', function ($q2) use($user_id) {
                        $q2->where('students.id', $user_id);
                    });
            });
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
            if ($with_tests) {
                $tests = [];
                foreach ($class->classApplications()->whereHas('test')->orderBy('start_date', 'ASC')->get() as $class_app) {
                    $test = $class_app->test()->first();
                    if (!$is_teacher && $class_app->is_for_selected_students) {
                        if (DB::table('classes_applications_students')
                                ->where('class_app_id', $class_app->id)
                                ->where('student_id', $this->user->id)->count() < 1) {
                            continue;
                        }
                    }
                    $test->icon = $test->icon();
                    $test->class_id = $class_id;
                    $test->app_id = $class_app && $class_app->app_id ? $class_app->app_id : 0;
                    $test->attempts = $class_app->attempts ?: 1;
                    $test->students = $this->getTestReportData($class, $class_app);
                    if ($class_app->start_date) {
                        $start_at = $class_app->start_time ? $class_app->start_date.' '.$class_app->start_time : $class_app->start_date.' 00:00:00';
                    } else {
                        $start_at = null;
                    }
                    $test->start_at = $start_at ? Carbon::parse($start_at)->format('Y-m-d g:i A') : null;
                    if ($class_app->due_date) {
                        $due_at = $class_app->due_time ? $class_app->due_date.' '.$class_app->due_time : $class_app->due_date.' 00:00:00';
                    } else {
                        $due_at = null;
                    }
                    $test->due_at = $due_at ? Carbon::parse($due_at)->format('Y-m-d g:i A') : null;
                    array_push($tests, $test);
                }
                $class_students = $is_teacher
                    ? array_values($class->students()->orderBy('email', 'ASC')->get(['email'])->toArray())
                    : [$this->user];
                return [
                    'class' => $class,
                    'assignments' => array_values($assignments->toArray()),
                    'students' => array_values($data->toArray()),
                    'tests' => $tests,
                    'class_students' => $class_students,
                ];
            } else {
                return [
                    'class' => $class,
                    'assignments' => array_values($assignments->toArray()),
                    'students' => array_values($data->toArray()),
                ];
            }
        }
        return null;
    }

    private function getTestReportData($class, $class_app) {
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
                    'end_at', students_test_attempts.end_at,
                    'is_error', students_test_attempts.is_error,
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
            'students_test_attempts.is_error',
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
                    'end_at' => $row->end_at,
                    'is_error' => $row->is_error
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
                            'end_at' => $row->end_at,
                            'is_error' => $row->is_error
                        ]
                    ]
                ];
            }
        }
        foreach ($students as $student) {
            $attempts = collect($student->attempts)->sortBy('attempt_no');
            $student->attempts = array_values($attempts->toArray());
            $completed_at = $attempts ? $attempts->sortByDesc('end_at')->first() : null;
            $student->completed_at = $completed_at && $completed_at->end_at ? Carbon::parse($completed_at->end_at)->format('Y-m-d g:i A') : null;
            $mark = $attempts ? $attempts->sortByDesc('mark')->first() : null;
            $student->mark = $mark ? $mark->mark : null;
        }
        return $students;
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

    /* public function getToDos($class_id) {
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
    } */
}
