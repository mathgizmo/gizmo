<?php

namespace App\Http\APIControllers;

use App\Application;
use App\Lesson;
use App\Setting;
use App\Student;
use App\StudentsTracking;
use App\Topic;
use App\Unit;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class TopicController extends Controller
{

    private $student;
    private $app;

    public function __construct()
    {
        $auth_user = JWTAuth::parseToken()->authenticate();
        $this->student = Student::find($auth_user->id);
        $this->app = Application::where('id', $this->student->app_id)->first();
        if (!$this->app) {
            abort(453, 'Application Not Selected!');
        }
    }

    public function index()
    {
        $student = $this->student;
        $app_id = $this->app->id;
        $mode = DB::connection()->getFetchMode();
        DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);
        $lessons_done = [];
        $topics_done = [];
        $units_done = [];
        $levels_done = [];
        foreach(DB::table('progresses')->select('entity_id', 'entity_type')->where(['student_id' => $student->id])
            ->whereIn('entity_type', ['topic','unit','level'])->where(function ($q) use ($app_id) {
                $q->where('app_id', $app_id)->orWhereNull('app_id');
            })->get() as $row) {
            switch ($row['entity_type']) {
                case 'topic':
                    $topics_done[] = $row['entity_id'];
                    break;
                case 'unit':
                    $units_done[] = $row['entity_id'];
                    break;
                case 'level':
                    $levels_done[] = $row['entity_id'];
                    break;
            }
        }
        $response =[];
        $levels = [];
        $units = [];
        $active_flag = true;
        $last_active_level_order = 0;
        foreach ($this->app->getLevels() as $level) {
            $level['active_flag'] = 1;
            $level['last_active_order'] = 0;
            if ($active_flag && in_array($level['id'], $levels_done)) {
                $level['status'] = 1;
            }
            else {
                if ($active_flag || $last_active_level_order == $level['order_no']) {
                    $level['status'] = 2;
                    if ($level['dependency'] == 1 && $student->is_super == 0) {
                        $active_flag = false;
                        $last_active_level_order = $level['order_no'];
                    }
                }
                else {
                    $level['status'] = 0;
                    $level['active_flag'] = 0;
                }
            }
            $level['units'] = [];
            $levels[$level['id']] = count($response);
            $response[] = $level;
        }
        foreach ($this->app->getUnits() as $unit) {
            if (!isset($levels[$unit['level_id']])) continue;
            $unit['topics'] = [];
            $l_element_id = $levels[$unit['level_id']];
            $units[$unit['id']] = array(count($response[$l_element_id]['units']), $l_element_id);
            $unit['active_flag'] = 1;
            $unit['last_active_order'] = 0;
            if ($response[$l_element_id]['active_flag'] && in_array($unit['id'], $units_done)) {
                $unit['status'] = 1;
            }
            else {
                if ($response[$l_element_id]['active_flag'] || $response[$l_element_id]['last_active_order'] == $unit['order_no']) {
                    $unit['status'] = 2;
                    if ($unit['dependency'] == 1 && $student->is_super == 0) {
                        $response[$l_element_id]['active_flag'] = false;
                        $response[$l_element_id]['last_active_order'] = $unit['order_no'];
                    }
                }
                else {
                    $unit['status'] = 0;
                    $unit['active_flag'] = 0;
                }
            }
            $response[$l_element_id]['units'][] = $unit;
        }
        foreach ($this->app->getTopics() as $topic) {
            try {
                $lessons_query = DB::table('lesson')->whereIn('id', function($q) use($app_id) {
                    $q->select('model_id')->from('application_has_models')->where('model_type', 'lesson')->where('app_id', $app_id);
                })->where('topic_id', $topic['id'])->where('dev_mode', 0);
                if ($lessons_query->count() > 0) {
                    $lessons_done[$topic['id']]['total'] = $lessons_query->count();
                    $lessons_done[$topic['id']]['done'] = $lessons_query->whereIn('id', function($q) use($student) {
                        $q->select('entity_id')->from('progresses')->where('entity_type', 'lesson')->where('student_id', $student->id);
                    })->count();
                } else {
                    $lessons_done[$topic['id']] = DB::table('lesson')
                        ->select(
                            'topic_id',
                            DB::raw("SUM(IF(lesson.dev_mode = 0, 1, 0)) as total"),
                            DB::raw("SUM(IF(progresses.id IS NULL, 0, 1)) as done")
                        )
                        ->leftJoin('progresses', function ($join) use ($student) {
                            $join->on('progresses.student_id', '=', DB::raw($student->id))
                                ->on('progresses.entity_type', '=', DB::raw('"lesson"'))
                                ->on('progresses.entity_id', '=', 'lesson.id');
                        })->where('topic_id', $topic['id'])->first();
                }
            } catch (\Exception $e) { }
            try {
                if($topic['icon_src'] == '' || !file_exists('../admin/'.$topic['icon_src'])) {
                    $topic['icon_src'] = 'images/default-icon.svg';
                }
            } catch (\Exception $e) {
                $topic['icon_src'] = 'images/default-icon.svg';
            }
            if (!isset($units[$topic['unit_id']])) continue;
            list($u_element_id, $l_element_id) = $units[$topic['unit_id']];
            $topic['order_id'] = $topic['order_no']?:floor(count($response[$l_element_id]['units'][$u_element_id]['topics'])/2);

            if ($response[$l_element_id]['units'][$u_element_id]['active_flag'] && in_array($topic['id'], $topics_done)) {
                $topic['status'] = 1;
            }
            else {
                if ($response[$l_element_id]['units'][$u_element_id]['active_flag'] ||
                    $response[$l_element_id]['units'][$u_element_id]['last_active_order'] == $topic['order_no']) {
                    $topic['status'] = 2;
                    if ($topic['dependency'] == 1 && $student->is_super == 0) {
                        $response[$l_element_id]['units'][$u_element_id]['active_flag'] = false;
                        $response[$l_element_id]['units'][$u_element_id]['last_active_order'] = $topic['order_no'];
                    }
                }
                else {
                    $topic['status'] = 0;
                }
            }
            $topic['progress'] = ['total' => 0, 'done' => 0, 'percent' => 0];
            if (isset($lessons_done[$topic['id']])) {
                $topic['progress'] = [
                    'total' => $lessons_done[$topic['id']]['total'],
                    'done' => $lessons_done[$topic['id']]['done'],
                    'percent' => ($lessons_done[$topic['id']]['total'] == 0)? 100 : round(100*$lessons_done[$topic['id']]['done']/$lessons_done[$topic['id']]['total'])
                ];
            }
            $response[$l_element_id]['units'][$u_element_id]['topics'][] = $topic;
        }
        DB::connection()->setFetchMode($mode);
        return $this->success($response);
    }

    public function get($id)
    {
        if(!$id || !is_numeric($id)) {
            return $this->error('id must be integer');
        }
        $student = $this->student;
        $app_id = $this->app->id;
        $mode = DB::connection()->getFetchMode();
        DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);
        $topic = DB::table('topic')->where('id', $id)->first();
        try {
            if($topic['icon_src'] == '' || !file_exists('../admin/'.$topic['icon_src'])) {
                $topic['icon_src'] = 'images/default-icon.svg';
            }
        } catch (Exception $e) {
            $topic['icon_src'] = 'images/default-icon.svg';
        }
        $unit = DB::table('unit')->where('id', $topic['unit_id'])->first();
        $level = DB::table('level')->where('id', $unit['level_id'])->first();
        $topic['unit'] = $unit['title'];
        $topic['level'] = $level['title'];
        if(!$topic) {
            return $this->error('topic not found');
        }
        $topic['lessons'] = $this->app->getLessons($id, $student->isAdmin());
        $lessons_ids = [];
        foreach($topic['lessons'] as $id => $lesson) {
            $lessons_ids[] = $lesson['id'];
            $topic['lessons'][$id]['status'] = 0;
        }
        $lessons_done = [];
        foreach(DB::table('progresses')->select('entity_id')->where(['student_id' => $student->id, 'entity_type' => 'lesson'])
            ->whereIn('entity_id', $lessons_ids)->get() as $row) {
                $lessons_done[] = $row['entity_id'];
        }
        $topic['status'] = count(
            DB::table('progresses')->select('entity_id')
                ->where(['student_id' => $student->id, 'entity_type' => 'topic'])
                ->where('entity_id', $topic['id'])
                ->where(function ($q) use ($app_id) {
                    $q->where('app_id', $app_id)->orWhereNull('app_id');
                })->get()
        );
        $active_flag = true;
        $last_active_order = 0;
        foreach ($topic['lessons'] as $id => $lesson) {
            //if all previous lessons with lower order_no not done, then lesson should be disabled
            if(!$active_flag) {
                if ($lesson['order_no'] > $last_active_order) break;
                $topic['lessons'][$id]['status'] = 2;
            }
            else {
                if(in_array($lesson['id'], $lessons_done)) {
                    $topic['lessons'][$id]['status'] = 1;
                }
                else {
                    $topic['lessons'][$id]['status'] = 2;
                    if($lesson['dependency'] == 1 && $lesson['dev_mode'] == 0 && $student->is_super == 0) {
                        $last_active_order = $lesson['order_no'];
                        $active_flag = false;
                    }
                }
            }
        }
        $ids = collect(DB::select("SELECT t.id FROM topic t JOIN unit u ON t.unit_id = u.id JOIN level l ON u.level_id = l.id ORDER BY l.order_no, l.id, u.order_no, u.id, t.order_no, t.id"))->pluck('id')->toArray();
        $topic_order_id = array_search($topic['id'], $ids);
        $app_topics = $this->app->getTopics($topic['unit_id']);
        if (count($app_topics) > 0) {
            $next = collect($app_topics)->filter(function ($item) use ($topic) {
                return $item['order_no'] > $topic['order_no'];
            })->first();
            $topic['next_topic_id'] = $next ? $next['id'] : 0;
        } else {
            $topic['next_topic_id'] = isset($ids[$topic_order_id+1]) ? $ids[$topic_order_id+1] : 0;
        }
        DB::connection()->setFetchMode($mode);
        return $this->success($topic);
    }

    public function getLesson($id, $lesson_id)
    {
        if(!$id || !is_numeric($id)) {
            return $this->error('id must be integer');
        }
        if(!$lesson_id || !is_numeric($lesson_id)) {
            return $this->error('lesson_id must be integer');
        }
        $mode = DB::connection()->getFetchMode();
        DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);
        $topic = DB::table('topic')->where('id', $id)->first();
        if(!$topic) {
            return $this->error('topic not found');
        }
        $lesson = DB::table('lesson')->where('id', $lesson_id)->where('topic_id', $id)->orderBy('id')->first();
        if(!$lesson) {
            return $this->error('lesson not found');
        }
        $unit = DB::table('unit')->where('id', $topic['unit_id'])->first();
        $level = DB::table('level')->where('id', $unit['level_id'])->first();
        $lesson['unit'] = $unit['title'];
        $lesson['level'] = $level['title'];
        $lesson['questions'] = DB::table('question')->where('lesson_id', $lesson_id)->get();
        $questions = [];
        foreach($lesson['questions'] as $index => $question) {
            $questions[$question['id']] = $index;
            $lesson['questions'][$index]['answers'] = [];
        }
        foreach(DB::table('answer')->whereIn('question_id', array_keys($questions))->get() as $answer) {
            $lesson['questions'][$questions[$answer['question_id']]]['answers'][] = $answer;
        }
        $lesson['topic'] = $topic;
        $app_id = $this->app->id;
        $next_lesson_query = DB::table('lesson')->whereIn('id', function($q) use($app_id) {
            $q->select('model_id')->from('application_has_models')->where('model_type', 'lesson')->where('app_id', $app_id);
        })->where('topic_id', $id)->where('dev_mode', '=', 0);
        if ($next_lesson_query->count() <= 0) {
            $next_lesson_query = DB::table('lesson')->where('topic_id', $id)->where('dev_mode', '=', 0);
        }
        $next = $next_lesson_query
            ->where('id', '!=', $lesson_id)->where(
                function ($query) use ($lesson) {
                    $query->where('order_no', '>', $lesson['order_no'])
                        ->orWhere(function ($query) use ($lesson) {
                            $query->where('order_no', '=', $lesson['order_no'])
                                ->where('id', '>', $lesson['id']);
                        });
                })
            ->orderBy('order_no', 'ASC')->orderBy('id', 'ASC')->first();
        $lesson['next_lesson_id'] = isset($next['id']) ? $next['id'] : 0;
        DB::connection()->setFetchMode($mode);
        return $this->success($lesson);
    }

    function testout($topic_id) {
        if (($model = Topic::find($topic_id)) == null) {
            return $this->error('Invalid topic.');
        }
        $mode = DB::connection()->getFetchMode();
        DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);
        $topic = $model->toArray();
        $topic_id = $topic['id'];
        $app_id = $this->app->id;
        $topic['questions'] = DB::table('question')->whereIn('lesson_id', function ($q1) use ($app_id, $topic_id) {
            $q1->select('id')->from('lesson')->whereIn('id', function($q2) use($app_id, $topic_id) {
                $q2->select('model_id')->from('application_has_models')->where('model_type', 'lesson')->where('app_id', $app_id);
            })->where('topic_id', $topic_id)->where('dependency', 1)->where('dev_mode', 0);
        })->inRandomOrder()->get(); // take(4)->get();
        if (count($topic['questions']) < 1) {
            $topic['questions'] = DB::table('question')->whereIn('lesson_id', function ($q1) use ($app_id, $topic_id) {
                $q1->select('id')->from('lesson')->where('topic_id', $topic_id)->where('dependency', 1)->where('dev_mode', 0);
            })->inRandomOrder()->get();
        }
        $lessons = $this->app->getLessons($topic_id);
        $questions = [];
        foreach($topic['questions'] as $id=>$question) {
            $questions[$question['id']] = $id;
            $topic['questions'][$id]['answers'] = [];
            $lesson_id = $topic['questions'][$id]['lesson_id'];
            $order_no = 0;
            for($i = 0; $i < count($lessons); $i++) {
                if ($lessons[$i]['id'] == $lesson_id) {
                    $order_no = $i+1;
                    break;
                }
            }
            $topic['questions'][$id]['order_no'] = $order_no;
            $topic['questions'][$id]['lesson_title'] = Lesson::where('id', $topic['questions'][$id]['lesson_id'])->first()->title;
        }
        foreach(DB::table('answer')->whereIn('question_id', array_keys($questions))->get() as $answer) {
            $topic['questions'][$questions[$answer['question_id']]]['answers'][] = $answer;
        }
        $ids = collect(DB::select("SELECT t.id FROM topic t JOIN unit u ON t.unit_id = u.id JOIN level l ON u.level_id = l.id ORDER BY l.order_no, l.id, u.order_no, u.id, t.order_no, t.id"))->pluck('id')->toArray();
        $topic_order_id = array_search($topic_id, $ids);
        $app_topics = $this->app->getTopics($model->unit_id);
        if (count($app_topics) > 0) {
            $next = collect($app_topics)->filter(function ($item) use ($topic) {
                return $item['order_no'] > $topic['order_no'];
            })->first();
            $topic['next_topic_id'] = $next ? $next['id'] : 0;
        } else {
            $topic['next_topic_id'] = isset($ids[$topic_order_id+1]) ? $ids[$topic_order_id+1] : 0;
        }
        $max_questions_num = Setting::where('key', 'topic_testout_max_questions_num')->first();
        $topic['max_questions_num'] = $max_questions_num ? intval($max_questions_num->value) : 5;
        $topic['lessons_count'] = count($lessons);
        DB::connection()->setFetchMode($mode);
        return $this->success($topic);
    }

    function testoutdone($topic) {
        if (($model = Topic::find($topic)) == null) {
            return $this->error('Invalid topic.');
        }
        StudentsTrackingController::topicProgressDone($model->id, $this->student);
        return $this->success('OK.');
    }

    function getLastVisitedLesson($student_id) {
        try {
            $lesson_id = StudentsTracking::where('student_id', $student_id)->orderBy('start_datetime', 'DESC')->first()->lesson_id;
            $lesson = Lesson::where('id', $lesson_id)->first();
            return $this->success($lesson);
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    function getLastVisitedTopic($student_id) {
        try {
            $lesson_id = StudentsTracking::where('student_id', $student_id)->orderBy('start_datetime', 'DESC')->first()->lesson_id;
            $topic_id = Lesson::where('id', $lesson_id)->first()->topic_id;
            $topic = Topic::where('id', $topic_id)->first();
            return $this->success($topic);
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    function getLastVisitedUnit($student_id) {
        try {
            $lesson_id = StudentsTracking::where('student_id', $student_id)->orderBy('start_datetime', 'DESC')->first()->lesson_id;
            $topic_id = Lesson::where('id', $lesson_id)->first()->topic_id;
            $unit_id = Topic::where('id', $topic_id)->first()->unit_id;
            $unit = Unit::where('id', $unit_id)->first();
            return $this->success($unit);
        } catch (\Exception $e) {
            return abort(404);
        }
    }
}
