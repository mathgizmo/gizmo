<?php

namespace App\Http\APIControllers;

use App\Application;
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
        if (request()->has('app_id')) {
            $app_id = request('app_id');
            $this->app = Application::where('id', $app_id)->first();
            if (!$this->app) {
                $this->app = Application::where('id', $this->student->app_id)->first();
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
        if ($app_id) {
            StudentsTracking::create([
                'student_id' => $this->student->id,
                'lesson_id' => $lesson,
                'app_id' => $app_id,
                'action' => 'start',
                'date' => $now,
                'start_datetime' => $now,
                'weak_questions' => json_encode([]),
                'ip' => request()->ip(),
                'user_agent' => request()->server('HTTP_USER_AGENT'),
            ]);
        }
        return $this->success($now);
    }

    function doneTestoutLessons($topic_id) {
        $topic = Topic::where('id', $topic_id)->first();
        if (!$topic) {
            return $this->error('Invalid topic.');
        }
        $app_id = $this->app ? $this->app->id : null;
        if (!$app_id) {
            return $this->success('No application provided.', 404);
        }
        $lastLesson = Lesson::where('id', request()->lesson_id)->first();
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
        return $this->success('OK.');
    }

    public function done($lesson, $is_testout = false)
    {
        if (($model = Lesson::find($lesson)) == null) {
            return $this->error('Invalid lesson.');
        }
        $app_id = $this->app ? $this->app->id : null;
        if (!$app_id) {
            return $this->success('No application provided.', 404);
        }
        $student = $this->student;
        StudentsTracking::create([
            'student_id' => $this->student->id,
            'lesson_id' => $lesson,
            'app_id' => $app_id,
            'action' => 'done',
            'date' => date("Y-m-d H:i:s"),
            'start_datetime' => date("Y-m-d H:i:s", (request()->start_datetime ? strtotime(request()->start_datetime) : date('U'))),
            'weak_questions' => json_encode(request()->weak_questions ? request()->weak_questions : []),
            'ip' => request()->ip(),
            'user_agent' => request()->server('HTTP_USER_AGENT'),
            'is_testout' => $is_testout
        ]);
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
        return $this->success('OK.');
    }

    public static function topicProgressDone($topic_id, $student, $app_id = null)
    {
        $student = Student::where('id', $student->id)->first();
        if (!$app_id) {
            $app_id = $student ? $student->app_id : null;
        }
        $completed_at = Carbon::now()->toDateTimeString();
        //mark topic as done
        try {
            DB::table('progresses')->insert([
                'student_id' => $student->id,
                'entity_type' => 'topic',
                'entity_id' => $topic_id,
                'app_id' => $app_id,
                'completed_at' => $completed_at
            ]);
        } catch (\Exception $e) { }
        //find all topics from unit that are not done yet
        $topic_model = Topic::where("id", $topic_id)->first();
        if(!$topic_model) { return; }
        $topic_query = DB::table('topic')->where(function ($query) use($app_id) {
            $query->whereIn('id', function($q1) use($app_id) {
                $q1->select('model_id')->from('application_has_models')->where('model_type', 'topic')->where('app_id', $app_id);
            })->orWhereIn('id', function($q2) use($app_id) {
                $q2->select('topic_id')->from('lesson')->whereIn('id', function($q3) use($app_id) {
                    $q3->select('model_id')->from('application_has_models')->where('model_type', 'lesson')->where('app_id', $app_id);
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
                ->where('progresses.app_id', $app_id)
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
            //find all units from level that are not done yet
            $unit_model = Unit::where('id', $topic_model->unit_id)->first();
            if(!$unit_model) { return; }
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
                    ->where('progresses.app_id', $app_id)->get()->all();
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
                //find all levels from application that are not done yet
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
                    //if all levels are done, mark app as done
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
                'is_right_answer' => request('is_right_answer'),
            ]);
        }
        return $this->success('saved!');
    }

}
