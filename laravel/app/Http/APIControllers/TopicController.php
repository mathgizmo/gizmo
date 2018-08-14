<?php

namespace App\Http\APIControllers;

use App\Topic;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class TopicController extends Controller
{
    /**
     * return tree of levels/units/topics.
     *
     * @return array
     */
    public function index()
    {
        $student = JWTAuth::parseToken()->authenticate();
        $mode = DB::connection()->getFetchMode();
        DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);
        $lessons_done = [];
        foreach(DB::table('lesson')->select('topic_id', DB::raw("COUNT(lesson.id) as total"), DB::raw("SUM(IF(progresses.id IS NULL, 0, 1)) as done"))
            ->leftJoin('progresses', function ($join) use ($student) {
                $join->on('progresses.student_id', '=', DB::raw($student->id))
                ->on('progresses.entity_type', '=', DB::raw(0))
                ->on('progresses.entity_id', '=', 'lesson.id');
            })
            ->groupBy('topic_id')->get()as $topic) {
                $lessons_done[$topic['topic_id']] = $topic;
        }
        $topics_done = [];
        $units_done = [];
        $levels_done = [];
        foreach(DB::table('progresses')->select('entity_id', 'entity_type')->where(['student_id' => $student->id])
            ->whereIn('entity_type', [1,2,3])->get() as $row) {
            switch ($row['entity_type']) {
                case 1:
                    $topics_done[] = $row['entity_id'];
                    break;
                case 2:
                    $units_done[] = $row['entity_id'];
                    break;
                case 3:
                    $levels_done[] = $row['entity_id'];
                    break;
            }
        }
        $response =[];
        $levels = [];
        $units = [];
        $active_flag = true;
        $last_active_level_order = 0;
        foreach (DB::select('select * from level order by order_no, id asc') as $level) {
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
        foreach (DB::select('select * from unit where dev_mode = 0 order by order_no, id asc') as $unit) {
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
        foreach (DB::select('select * from topic where dev_mode = 0 order by order_no, id asc') as $topic) {
            try {
                if($topic['icon_src'] == '' || !file_exists('../admin/'.$topic['icon_src'])) {
                    $topic['icon_src'] = 'images/default-icon.svg';
                }
            } catch (Exception $e) {
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
                    'percent' => round(100*$lessons_done[$topic['id']]['done']/$lessons_done[$topic['id']]['total'])
                ];
            }
            $response[$l_element_id]['units'][$u_element_id]['topics'][] = $topic;
        }
        DB::connection()->setFetchMode($mode);
        return $this->success($response);
    }

    /**
     * return tree of lessons for given topics.
     *
     * @param $id
     * @return array
     */
    public function get($id)
    {
        if(!$id || !is_numeric($id)) {
            return $this->error('id must be integer');
        }
        $student = JWTAuth::parseToken()->authenticate();
        $mode = DB::connection()->getFetchMode();
        DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);
        $topic = DB::table('topic')->where('id', $id)->first();
        $unit = DB::table('unit')->where('id', $topic['unit_id'])->first();
        $level = DB::table('level')->where('id', $unit['level_id'])->first();
        $topic['unit'] = $unit['title'];
        $topic['level'] = $level['title'];
        if(!$topic) {
            return $this->error('topic not found');
        }
        $query = DB::table('lesson')->where('topic_id', $id);
        if (!$student->is_admin()) {
            $query->where('dev_mode', 0);
        }
        $topic['lessons'] = $query->orderBy('order_no')->orderBy('id')->get();
        $lessons_ids = [];
        foreach($topic['lessons'] as $id => $lesson) {
            $lessons_ids[] = $lesson['id'];
            $topic['lessons'][$id]['status'] = 0;
        }
        $lessons_done = [];
        foreach(DB::table('progresses')->select('entity_id')->where(['student_id' => $student->id, 'entity_type' => 0])
            ->whereIn('entity_id', $lessons_ids)->get() as $row) {
                $lessons_done[] = $row['entity_id'];
        }
        $topic['status'] = count(DB::table('progresses')->select('entity_id')->where(['student_id' => $student->id, 'entity_type' => 1])
            ->where('entity_id', $topic['id'])->get());
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
        $topic['next_topic_id'] = isset($ids[$topic_order_id+1]) ? $ids[$topic_order_id+1] : 0;
        DB::connection()->setFetchMode($mode);
        return $this->success($topic);
    }

    /**
     * return tree of questions for given lesson.
     *
     * @param $id
     * @param $lesson_id
     * @return array
     */
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
        $next = (DB::table('lesson')->where('id', '!=', $lesson_id)
            ->where('topic_id', $id)->where('dev_mode', '=', 0)->where(
                function ($query) use ($lesson) {
                    $query->where('order_no', '>', $lesson['order_no'])
                    ->orWhere(function ($query) use ($lesson) {
                        $query->where('order_no', '=', $lesson['order_no'])
                        ->where('id', '>', $lesson['id']);
                    });
                })
            ->first());
        $lesson['next_lesson_id'] = isset($next['id']) ? $next['id'] : 0;
        DB::connection()->setFetchMode($mode);
        return $this->success($lesson);
    }

    /**
     * @param $topic_id
     * @return mixed
     */
    function testout($topic_id) {
        if (($model = Topic::find($topic_id)) == null) {
            return $this->error('Invalid topic.');
        }
        $mode = DB::connection()->getFetchMode();
        DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);
        $topic = $model->toArray();
        $topic_id = $topic['id'];
        $topic['questions'] = DB::table('question')
            ->select('question.*')
            ->join(DB::raw('(SELECT id FROM lesson WHERE topic_id = ' . $topic_id . ' AND dependency = 1 ORDER BY order_no DESC, id DESC LIMIT 2) l'), function($join)
            {
                $join->on('question.lesson_id', '=', 'l.id');
            })
            ->inRandomOrder()->take(4)->get();
        $questions = [];
        foreach($topic['questions'] as $id=>$question) {
            $questions[$question['id']] = $id;
            $topic['questions'][$id]['answers'] = [];
        }
        foreach(DB::table('answer')->whereIn('question_id', array_keys($questions))->get() as $answer) {
            $topic['questions'][$questions[$answer['question_id']]]['answers'][] = $answer;
        }
        $ids = collect(DB::select("SELECT t.id FROM topic t JOIN unit u ON t.unit_id = u.id JOIN level l ON u.level_id = l.id ORDER BY l.order_no, l.id, u.order_no, u.id, t.order_no, t.id"))->pluck('id')->toArray();
        $topic_order_id = array_search($topic_id, $ids);
        $topic['next_topic_id'] = isset($ids[$topic_order_id+1]) ? $ids[$topic_order_id+1] : 0;
        DB::connection()->setFetchMode($mode);
        return $this->success($topic);
    }

    /**
     * @param $topic
     * @return mixed
     */
    function testoutdone($topic) {
        if (($model = Topic::find($topic)) == null) {
            return $this->error('Invalid topic.');
        }
        $student = JWTAuth::parseToken()->authenticate();
        StudentsTrackingController::topicProgressDone($model->id, $student);
        return $this->success('OK.');
    }
}
