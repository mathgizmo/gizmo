<?php

namespace App\Http\APIControllers;

use App\Application;
use App\Progress;
use App\Student;
use App\StudentsTracking;
use App\Topic;
use App\Unit;
use JWTAuth;
use App\Lesson;
use Illuminate\Support\Facades\DB;

class StudentsTrackingController extends Controller
{

    private $student;
    private $app;

    public function __construct()
    {
        $auth_user = JWTAuth::parseToken()->authenticate();
        $this->student = Student::find($auth_user->id);
        $this->app = Application::where('id', $this->student->app_id)->first();
    }

    public function start($lesson)
    {
        if (($model = Lesson::find($lesson)) == null) {
            return $this->error('Invalid lesson.');
        }
        $now = date("Y-m-d H:i:s");
        StudentsTracking::create([
            'student_id' => $this->student->id,
            'lesson_id' => $lesson,
            'action' => 'start',
            'date' => $now,
            'start_datetime' => $now,
            'weak_questions' => json_encode([]),
            'ip' => request()->ip(),
            'user_agent' => request()->server('HTTP_USER_AGENT'),
        ]);
        return $this->success($now);
    }

    public function done($lesson)
    {
        if (($model = Lesson::find($lesson)) == null) {
            return $this->error('Invalid lesson.');
        }
        $app_id = $this->app->id;
        $student = $this->student;
        StudentsTracking::create([
            'student_id' => $this->student->id,
            'lesson_id' => $lesson,
            'action' => 'done',
            'date' => date("Y-m-d H:i:s"),
            'start_datetime' => date("Y-m-d H:i:s", (request()->start_datetime ? strtotime(request()->start_datetime) : date('U'))),
            'weak_questions' => json_encode(request()->weak_questions ? request()->weak_questions : []),
            'ip' => request()->ip(),
            'user_agent' => request()->server('HTTP_USER_AGENT'),
        ]);
        $progress_data = [
            'student_id' => $this->student->id,
            'entity_type' => 0,
            'entity_id' => $lesson
        ];
        $progress = Progress::where($progress_data)->get();
        if ($progress->count() == 0) {
            DB::enableQueryLog();
            Progress::create($progress_data);
            //find all lessons from topic that are not done yet
            $lessons_query = DB::table('lesson')->whereIn('id', function($q) use($app_id) {
                $q->select('model_id')->from('application_has_models')->where('model_type', 'lesson')->where('app_id', $app_id);
            })->where('topic_id', $model->topic_id)->where('dev_mode', 0);
            if ($lessons_query->count() > 0) {
                $is_topic_done = $lessons_query->count() <= $lessons_query->whereIn('id', function($q) use($student) {
                        $q->select('entity_id')->from('progresses')->where('entity_type', 0)->where('student_id', $student->id);
                    })->count();
            } else {
                $lessons = DB::table('lesson')->leftJoin('progresses', function ($join) use ($student) {
                    $join->on('progresses.student_id', '=', DB::raw($student->id))
                        ->on('progresses.entity_type', '=', DB::raw(0))
                        ->on('progresses.entity_id', '=', 'lesson.id');
                })->where(['topic_id' => $model->topic_id, 'dependency' => 1])->where('dev_mode', 0)->whereNull('progresses.id')->get();
                $is_topic_done = !count($lessons);
            }
            // if all lessons done
            if ($is_topic_done) {
                self::topicProgressDone($model->topic_id, $student);
            }
        }
        return $this->success('OK.');
    }

    public static function topicProgressDone($topic_id, $student)
    {
        $student = Student::where('id', $student->id)->first();
        $app_id = $student ? $student->app_id : null;
        //mark topic as done
        $progress_data = [
            'student_id' => $student->id,
            'entity_type' => 1,
            'entity_id' => $topic_id
        ];
        DB::insert('INSERT IGNORE INTO progresses(student_id, entity_type, entity_id) '.
            'values (?, ?, ?)', array_values($progress_data));
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
            $is_unit_done = $topic_query->count() <= $topic_query->whereIn('id', function($q) use($student) {
                    $q->select('entity_id')->from('progresses')->where('entity_type', 1)->where('student_id', $student->id);
                })->count();
        } else {
            $topics = DB::table('topic')->leftJoin('progresses', function ($join) use ($student) {
                $join->on('progresses.student_id', '=', DB::raw($student->id))
                    ->on('progresses.entity_type', '=', DB::raw(1))
                    ->on('progresses.entity_id', '=', 'topic.id');
            })->where(['unit_id' => $topic_model->unit_id, 'dependency' => 1])->whereNull('progresses.id')->get();
            $is_unit_done = !count($topics);
        }
        // if all topics are done, mark unit as done
        if ($is_unit_done) {
            $progress_data['entity_type'] = 2;
            $progress_data['entity_id'] = $topic_model->unit_id;
            DB::insert('INSERT IGNORE INTO progresses(student_id, entity_type, entity_id) '.
                'values (?, ?, ?)', array_values($progress_data));
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
                $is_level_done = $unit_query->count() <= $unit_query->whereIn('id', function($q) use($student) {
                        $q->select('entity_id')->from('progresses')->where('entity_type', 2)->where('student_id', $student->id);
                    })->count();
            } else {
                $units = DB::table('unit')->leftJoin('progresses', function ($join) use ($student) {
                    $join->on('progresses.student_id', '=', DB::raw($student->id))
                        ->on('progresses.entity_type', '=', DB::raw(2))
                        ->on('progresses.entity_id', '=', 'unit.id');
                })->where(['level_id' => $unit_model->level_id, 'dependency' => 1])->whereNull('progresses.id')->get();
                $is_level_done = !count($units);
            }
            //if all units are done, mark level as done
            if ($is_level_done) {
                $progress_data['entity_type'] = 3;
                $progress_data['entity_id'] = $unit_model->level_id;
                DB::insert('INSERT IGNORE INTO progresses(student_id, entity_type, entity_id) '.
                    'values (?, ?, ?)', array_values($progress_data));
            }
        }
    }
}
