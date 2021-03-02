<?php

namespace App\Http\APIControllers;

use App\Application;
use App\ClassApplication;
use App\ClassApplicationStudent;
use App\Level;
use App\Progress;
use App\Question;
use App\Student;
use App\StudentsTracking;
use App\StudentsTrackingQuestion;
use App\Topic;
use App\Unit;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Lesson;
use Illuminate\Support\Facades\DB;

class StudentsTrackingController extends Controller
{

    private $student;
    private $app;
    private $class_app;

    public function __construct()
    {
        try {
            $auth_user = JWTAuth::parseToken()->authenticate();
            if (!$auth_user) {
                abort(401, 'Unauthorized!');
            }
            $this->student = Student::find($auth_user->id);
            if (!$this->student) {
                abort(401, 'Unauthorized!');
            }
        } catch (\Exception $e) {
            abort(401, 'Unauthorized!');
        }
        if (request()->has('class_app_id')) {
            $this->class_app = ClassApplication::where('id', request('class_app_id'))->first();
            $this->app = $this->class_app ? Application::where('id', $this->class_app->app_id)->first() : null;
        } else if (request()->has('app_id')) {
            $app_id = request('app_id');
            if ($app_id == 0) {
                $this->app = new Application();
                $this->app->id = 0;
                $this->app->name = 'Content Review';
                $this->app->teacher_id = null;
                $this->app->question_num = 0;
                $this->app->testout_attempts = -1;
                $this->app->allow_any_order = true;
            } else {
                $this->app = Application::where('id', $app_id)->first();
                if (!$this->app) {
                    $this->app = Application::where('id', $this->student->app_id)->first();
                }
            }
        } else {
            $this->app = Application::where('id', $this->student->app_id)->first();
        }
    }

    public function start($lesson)
    {
        if (($model = Lesson::find($lesson)) == null) {
            return $this->error('Invalid lesson.');
        }
        $now = date("Y-m-d H:i:s");
        $app_id = $this->app ? $this->app->id : null;
        StudentsTracking::create([
            'student_id' => $this->student->id,
            'lesson_id' => $lesson,
            'app_id' => $app_id ?: null,
            'action' => 'start',
            'date' => $now,
            'start_datetime' => $now,
            'weak_questions' => json_encode([]),
            'ip' => request()->ip(),
            'user_agent' => request()->server('HTTP_USER_AGENT'),
        ]);
        return $this->success($now, 200);
    }

    function doneTestoutLessons($topic_id) {
        $topic = Topic::where('id', $topic_id)->first();
        if (!$topic) {
            return $this->error('Invalid topic.');
        }
        $app_id = $this->app ? $this->app->id : null;
        if (!$app_id) {
            return $this->error('No application provided.', 404);
        }
        $this->app->incrementTestoutAttempts($this->student->id, $topic_id);
        $lastLesson = Lesson::where('id', request()->lesson_id)->first();
        if (!$lastLesson && request()->lesson_id) {
            return $this->error('Lesson not found.', 404);
        }
        $student = $this->student;
        // find all lessons from topic that are not done yet
        $lessons_query = DB::table('lesson')->whereIn('id', function($q) use($app_id) {
            $q->select('model_id')->from('application_has_models')->where('model_type', 'lesson')->where('app_id', $app_id);
        })->where('topic_id', $topic_id)->where('dev_mode', 0);
        if ($lessons_query->count() > 0) {
            if ($lastLesson) {
                $lessons = $lessons_query->where('order_no', '<', $lastLesson->order_no)->get();
            } else {
                $lessons = $lessons_query->get();
            }
        } else {
            $lessons_rows = DB::table('lesson')->leftJoin('progresses', function ($join) use ($student, $app_id) {
                $join->on('progresses.student_id', '=', DB::raw($student->id))
                    ->on('progresses.entity_type', '=', DB::raw('"lesson"'))
                    ->on('progresses.entity_id', '=', 'lesson.id')
                    ->on('progresses.app_id', '=', DB::raw($app_id));
            })->where(['topic_id' => $topic_id])
                ->where('dev_mode', 0)
                ->whereNull('progresses.id')->get(['lesson.id', 'lesson.order_no'])->all();
            if ($lastLesson) {
                $lessons = collect($lessons_rows)->where('order_no', '<', $lastLesson->order_no);
            } else {
                $lessons = collect($lessons_rows);
            }
        }
        foreach ($lessons as $lesson) {
            $progress_data = [
                'student_id' => $this->student->id,
                'entity_type' => 'lesson',
                'entity_id' => $lesson->id,
                'app_id' => $app_id
            ];
            if (Progress::where($progress_data)->count() == 0) {
                $this->done($lesson->id, true);
            }
        }
        $message = StudentsTrackingController::checkIfApplicationIsComplete($this->student->id, $this->app->id);
        return $this->success($message, 200);
    }

    public function done($lesson, $is_testout = false)
    {
        if (($model = Lesson::find($lesson)) == null) {
            return $this->error('Invalid lesson.');
        }
        $app_id = $this->app ? $this->app->id : null;
        $student = $this->student;
        StudentsTracking::create([
            'student_id' => $this->student->id,
            'lesson_id' => $lesson,
            'app_id' => $app_id ?: null,
            'action' => 'done',
            'date' => date("Y-m-d H:i:s"),
            'start_datetime' => date("Y-m-d H:i:s", (request()->start_datetime ? strtotime(request()->start_datetime) : date('U'))),
            'weak_questions' => json_encode(request()->weak_questions ? request()->weak_questions : []),
            'ip' => request()->ip(),
            'user_agent' => request()->server('HTTP_USER_AGENT'),
            'is_testout' => $is_testout
        ]);
        if (!$app_id) {
            return $this->success('Done!', 200);
        }
        $progress_data = [
            'student_id' => $this->student->id,
            'entity_type' => 'lesson',
            'entity_id' => $lesson,
            'app_id' => $app_id
        ];
        if (Progress::where($progress_data)->count() == 0) {
            try {
                Progress::create(array_merge($progress_data, ['completed_at' => Carbon::now()->toDateTimeString()]));
            } catch (\Exception $e) { }
        }
        // find all lessons from topic that are not done yet
        $lessons_query = DB::table('lesson')->whereIn('id', function($q) use($app_id) {
            $q->select('model_id')->from('application_has_models')->where('model_type', 'lesson')->where('app_id', $app_id);
        })->where('topic_id', $model->topic_id)->where('dev_mode', 0);
        if ($lessons_query->count() > 0) {
            $is_topic_done = $lessons_query->count() <= $lessons_query->whereIn('id', function($q) use($student, $app_id) {
                    $q->select('entity_id')->from('progresses')
                        ->where('entity_type', 'lesson')
                        ->where('student_id', $student->id)
                        ->where('app_id', $app_id);
                })->count();
        } else {
            $lessons = DB::table('lesson')->leftJoin('progresses', function ($join) use ($student, $app_id) {
                $join->on('progresses.student_id', '=', DB::raw($student->id))
                    ->on('progresses.entity_type', '=', DB::raw('"lesson"'))
                    ->on('progresses.entity_id', '=', 'lesson.id')
                    ->on('progresses.app_id', '=', DB::raw($app_id));
            })
                ->where(['topic_id' => $model->topic_id, 'dependency' => 1])
                ->where('dev_mode', 0)
                ->whereNull('progresses.id')->get()->all();
            $is_topic_done = !count($lessons);
        }
        // if all lessons done
        if ($is_topic_done) {
            self::topicProgressDone($model->topic_id, $student, $app_id);
        }
        $message = StudentsTrackingController::checkIfApplicationIsComplete($this->student->id, $this->app->id);
        return $this->success($message, 200);
    }

    public static function checkIfApplicationIsComplete($student_id, $app_id) {
        $is_assignment_complete = false;
        $correct_question_rate = 0;
        $assignment_name = '';
        $is_assignment_complete = DB::table('progresses')
                ->where('student_id', $student_id)
                ->where('entity_id', $app_id)
                ->where('entity_type', 'application')->count() > 0;
        if ($is_assignment_complete) {
            $app = Application::where('id', $app_id)->first();
            if ($app) {
                $assignment_name = $app->name;
            }
            $tracking_questions_statistics = DB::table('students_tracking_questions')->select(
                DB::raw("SUM(1) as total"),
                DB::raw("SUM(IF(is_right_answer, 1, 0)) as complete")
            )->where('app_id', $app_id)->where('student_id', $student_id)->first();
            $correct_question_rate = $tracking_questions_statistics->total ? $tracking_questions_statistics->complete / $tracking_questions_statistics->total : 1;
        }
        return [
            'is_assignment_complete' => $is_assignment_complete,
            'correct_question_rate' => $correct_question_rate,
            'assignment_name' => $assignment_name
        ];
    }

    public static function topicProgressDone($topic_id, $student, $app_id = null)
    {
        $student = Student::where('id', $student->id)->first();
        if (!$app_id) {
            $app_id = $student ? $student->app_id : null;
        }
        $completed_at = Carbon::now()->toDateTimeString();
        // mark topic as done
        try {
            DB::table('progresses')->insert([
                'student_id' => $student->id,
                'entity_type' => 'topic',
                'entity_id' => $topic_id,
                'app_id' => $app_id,
                'completed_at' => $completed_at
            ]);
        } catch (\Exception $e) { }
        // find all topics from unit that are not done yet
        $topic_model = Topic::where('id', $topic_id)->first();
        if (!$topic_model) { return; }
        $topic_query = DB::table('topic')->where(function ($query) use($app_id) {
            $query->whereIn('id', function($q1) use($app_id) {
                $q1->select('model_id')->from('application_has_models')->where('model_type', 'topic')->where('app_id', $app_id);
            })->orWhereIn('id', function($q2) use($app_id) {
                $q2->select('topic_id')->from('lesson')->whereIn('id', function($q3) use($app_id) {
                    $q3->select('model_id')->from('application_has_models')->where('model_type', 'lesson')->where('app_id', $app_id);
                });
            })->orWhereIn('id', function($q4) use($app_id) {
                $q4->select('id')->from('topic')->whereIn('unit_id', function($q5) use($app_id) {
                    $q5->select('model_id')->from('application_has_models')->where('model_type', 'unit')->where('app_id', $app_id);
                });
            })->orWhereIn('id', function($q6) use($app_id) {
                $q6->select('id')->from('topic')->whereIn('unit_id', function($q7) use($app_id) {
                    $q7->select('id')->from('unit')->whereIn('level_id', function($q8) use($app_id) {
                        $q8->select('model_id')->from('application_has_models')->where('model_type', 'level')->where('app_id', $app_id);
                    });
                });
            });
        })->where('unit_id', $topic_model->unit_id)->where('dev_mode', 0);
        if ($topic_query->count() > 0) {
            $is_unit_done = $topic_query->count() <= $topic_query->whereIn('id', function($q) use($student, $app_id) {
                    $q->select('entity_id')->from('progresses')
                        ->where('entity_type', 'topic')
                        ->where('student_id', $student->id)
                        ->where('app_id', $app_id);
                })->count();
        } else {
            $topics_count = DB::table('topic')->leftJoin('progresses', function ($join) use ($student, $app_id) {
                $join->on('progresses.student_id', '=', DB::raw($student->id))
                    ->on('progresses.entity_type', '=', DB::raw('"topic"'))
                    ->on('progresses.entity_id', '=', 'topic.id')
                    ->on('progresses.app_id', '=', DB::raw($app_id));
            })->where(['unit_id' => $topic_model->unit_id, 'dependency' => 1])
                ->whereNull('progresses.id')
                ->count();
            $is_unit_done = !$topics_count;
        }
        // if all topics are done, mark unit as done
        if ($is_unit_done) {
            try {
                DB::table('progresses')->insert([
                    'student_id' => $student->id,
                    'entity_type' => 'unit',
                    'entity_id' => $topic_model->unit_id,
                    'app_id' => $app_id,
                    'completed_at' => $completed_at
                ]);
            } catch (\Exception $e) { }
            // find all units from level that are not done yet
            $unit_model = Unit::where('id', $topic_model->unit_id)->first();
            if (!$unit_model) { return; }
            $unit_query = DB::table('unit')->where(function ($query) use($app_id) {
                $query->whereIn('id', function($q1) use($app_id) {
                    $q1->select('model_id')->from('application_has_models')->where('model_type', 'unit')->where('app_id', $app_id);
                })->orWhereIn('id', function($q2) use($app_id) {
                    $q2->select('unit_id')->from('topic')->whereIn('id', function($q3) use($app_id) {
                        $q3->select('model_id')->from('application_has_models')->where('model_type', 'topic')->where('app_id', $app_id);
                    });
                })->orWhereIn('id', function($q4) use($app_id) {
                    $q4->select('unit_id')->from('topic')->whereIn('id', function($q5) use($app_id) {
                        $q5->select('topic_id')->from('lesson')->whereIn('id', function($q6) use($app_id) {
                            $q6->select('model_id')->from('application_has_models')->where('model_type', 'lesson')->where('app_id', $app_id);
                        });
                    });
                })->orWhereIn('id', function($q7) use($app_id) {
                    $q7->select('id')->from('unit')->whereIn('level_id', function($q8) use($app_id) {
                        $q8->select('model_id')->from('application_has_models')->where('model_type', 'level')->where('app_id', $app_id);
                    });
                });
            })->where('level_id', $unit_model->level_id)->where('dev_mode', 0);
            if ($unit_query->count() > 0) {
                $is_level_done = $unit_query->count() <= $unit_query->whereIn('id', function($q) use($student, $app_id) {
                        $q->select('entity_id')->from('progresses')
                            ->where('entity_type', 'unit')
                            ->where('student_id', $student->id)
                            ->where('app_id', $app_id);
                    })->count();
            } else {
                $units = DB::table('unit')->leftJoin('progresses', function ($join) use ($student, $app_id) {
                    $join->on('progresses.student_id', '=', DB::raw($student->id))
                        ->on('progresses.entity_type', '=', DB::raw('"unit"'))
                        ->on('progresses.entity_id', '=', 'unit.id')
                        ->on('progresses.app_id', '=', DB::raw($app_id));
                })
                    ->where(['level_id' => $unit_model->level_id, 'dependency' => 1])
                    ->whereNull('progresses.id')
                    ->get()->all();
                $is_level_done = !count($units);
            }
            //if all units are done, mark level as done
            if ($is_level_done) {
                try {
                    DB::table('progresses')->insert([
                        'student_id' => $student->id,
                        'entity_type' => 'level',
                        'entity_id' => $unit_model->level_id,
                        'app_id' => $app_id,
                        'completed_at' => $completed_at
                    ]);
                } catch (\Exception $e) { }
                // find all levels from application that are not done yet
                $level_model = Level::where('id', $unit_model->level_id)->first();
                if (!$level_model) { return; }
                $level_query = (Application::where('id', $app_id)->first())->getLevelsQuery()->where('dev_mode', 0);
                if ($level_query->count() > 0) {
                    $is_app_done = $level_query->count() <= $level_query->whereIn('id', function($q) use($student, $app_id) {
                            $q->select('entity_id')->from('progresses')
                                ->where('entity_type', 'level')
                                ->where('student_id', $student->id)
                                ->where('app_id', $app_id);
                        })->count();
                    // if all levels are done, mark app as done
                    if ($is_app_done) {
                        try {
                            DB::table('progresses')->insert([
                                'student_id' => $student->id,
                                'entity_type' => 'application',
                                'entity_id' => $app_id,
                                'app_id' => $app_id,
                                'completed_at' => $completed_at
                            ]);
                        } catch (\Exception $e) { }
                    }
                }
            }
        }
    }

    public function trackQuestionAnswer($question_id)
    {
        if (($model = Question::find($question_id)) == null) {
            return $this->error('Invalid question.');
        }
        $app_id = $this->app ? $this->app->id : null;
        if ($app_id) {
            StudentsTrackingQuestion::create([
                'student_id' => $this->student->id,
                'question_id' => $question_id,
                'app_id' => $app_id,
                'class_app_id' => $this->class_app ? $this->class_app->id : null,
                'is_right_answer' => request('is_right_answer'),
            ]);
            if ($this->app && $this->app->type == 'test' && $this->class_app) {
                $test_student = ClassApplicationStudent::where('class_app_id', $this->class_app->id)
                    ->where('student_id', $this->student->id)->first();
                $current_attempt = $test_student ?
                    DB::table('students_test_attempts')
                        ->where('test_student_id', $test_student->id)
                        ->whereNull('end_at')
                        ->first() : null;
                if (!$current_attempt) {
                    return $this->error('This test has been reset by the teacher, if you think an error has been made please take a screenshot of your progress!', 404);
                }
                DB::table('students_test_questions')
                    // ->where('class_app_id', $this->class_app->id)
                    // ->where('student_id', $this->student->id)
                    ->where('question_id', $question_id)
                    ->where('attempt_id', $current_attempt->id)
                    ->update([
                        'is_answered' => true,
                        'is_right_answer' => request('is_right_answer')
                    ]);
            }
        }
        return $this->success('saved!');
    }

}
