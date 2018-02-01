<?php

namespace App\Http\APIControllers;

use App\Http\Requests;
use Illuminate\Http\Request;
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
                    if ($level['dependency'] == 1) {
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

        foreach (DB::select('select * from unit order by order_no, id asc') as $unit) {
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
                    if ($unit['dependency'] == 1) {
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

        foreach (DB::select('select * from topic order by order_no, id asc') as $topic) {
            list($u_element_id, $l_element_id) = $units[$topic['unit_id']];
            $topic['order_id'] = $topic['order_no']?:floor(count($response[$l_element_id]['units'][$u_element_id]['topics'])/2);

            if ($response[$l_element_id]['units'][$u_element_id]['active_flag'] && in_array($topic['id'], $topics_done)) {
                $topic['status'] = 1;
            }
            else {
                if ($response[$l_element_id]['units'][$u_element_id]['active_flag'] ||
                    $response[$l_element_id]['units'][$u_element_id]['last_active_order'] == $topic['order_no']) {
                    $topic['status'] = 2;
                    if ($topic['dependency'] == 1) {
                        $response[$l_element_id]['units'][$u_element_id]['active_flag'] = false;
                        $response[$l_element_id]['units'][$u_element_id]['last_active_order'] = $topic['order_no'];
                    }
                }
                else {
                    $topic['status'] = 0;
                }
            }
            $response[$l_element_id]['units'][$u_element_id]['topics'][] = $topic;
        }
        DB::connection()->setFetchMode($mode);


        return $this->success($response);
    }

    /**
     * return tree of lessons for given topics.
     *
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
        $topic = DB::table('topic')->where('id',$id)->first();
        if(!$topic) {
            return $this->error('topic not found');
        }
        $topic['lessons'] = DB::table('lesson')->where('topic_id',$id)->orderBy('id')->get();
        $lessons_ids = [];
        foreach($topic['lessons'] as $id => $lesson) {
            $lessons_ids[] = $lesson['id'];
            $topic['lessons'][$id]['status'] = 0;
        }

        $lessons_done = [];
        foreach(DB::table('progresses')->select('entity_id')->where(['student_id' => $student->id, 'entity_type' => 0])
            ->whereIn('entity_id',$lessons_ids)->get() as $row) {
                $lessons_done[] = $row['entity_id'];
        }
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
                    if($lesson['dependency'] == 1) {
                        $last_active_order = $lesson['order_no'];
                        $active_flag = false;
                    }
                }
            }
        }


        DB::connection()->setFetchMode($mode);
        return $this->success($topic);
    }

    /**
     * return tree of questions for given lesson.
     *
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
        $topic = DB::table('topic')->where('id',$id)->first();
        if(!$topic) {
            return $this->error('topic not found');
        }

        $lesson = DB::table('lesson')->where('id',$lesson_id)->where('topic_id', $id)->orderBy('id')->first();
        if(!$lesson) {
            return $this->error('lesson not found');
        }
        $lesson['questions'] = DB::table('question')->where('lesson_id',$lesson_id)->get();
        $questions = [];
        foreach($lesson['questions'] as $id=>$question) {
            $questions[$question['id']] = $id;
            $lesson['questions'][$id]['answers'] = [];
        }

        foreach(DB::table('answer')->whereIn('question_id',array_keys($questions))->get() as $answer) {
            $lesson['questions'][$questions[$answer['question_id']]]['answers'][] = $answer;
        }

        $lesson['topic'] = $topic;

        DB::connection()->setFetchMode($mode);
        return $this->success($lesson);
    }
}
