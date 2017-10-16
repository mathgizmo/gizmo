<?php

namespace App\Http\APIControllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopicController extends Controller
{
    /**
     * return tree of levels/units/topics.
     *
     * @return array
     */
    public function index()
    {
        $mode = DB::connection()->getFetchMode();
        DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);
        $response =[];
        $levels = [];
        $units = [];
        foreach (DB::select('select * from level order by id asc') as $level) {
            $level['units'] = [];
            $levels[$level['id']] = count($response);
            $response[] = $level;
        }
        foreach (DB::select('select * from unit order by id asc') as $unit) {
            $unit['topics'] = [];
            $l_element_id = $levels[$unit['level_id']];
            $units[$unit['id']] = array(count($response[$l_element_id]['units']), $l_element_id);
            $response[$l_element_id]['units'][] = $unit;
        }
        foreach (DB::select('select * from topic order by order_no, id asc') as $topic) {
            list($u_element_id, $l_element_id) = $units[$topic['unit_id']];
            $topic['order_id'] = $topic['order_no']?:floor(count($response[$l_element_id]['units'][$u_element_id]['topics'])/2);
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

        $mode = DB::connection()->getFetchMode();
        DB::connection()->setFetchMode(\PDO::FETCH_ASSOC);
        $topic = DB::table('topic')->where('id',$id)->first();
        if(!$topic) {
            return $this->error('topic not found');
        }
        $topic['lessons'] = DB::table('lesson')->where('topic_id',$id)->orderBy('id')->get();

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

        foreach(DB::table('answers')->whereIn('question_id',array_keys($questions))->get() as $answer) {
            $lesson['questions'][$questions[$answer['question_id']]]['answers'][] = $answer;
        }

        $lesson['topic'] = $topic;

        DB::connection()->setFetchMode($mode);
        return $this->success($lesson);
    }
}
