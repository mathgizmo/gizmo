<?php

namespace App\Http\APIControllers;

use App\Http\Requests;
use App\PlacementQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class PlacementController extends Controller
{
    
    /**
     * return placement questions.
     *
     * @return array
     */
    public function get()
    {
        $placement = PlacementQuestion::all();
        return $this->success($placement);
    }

    /**
     * return first topic id.
     *
     * @return int
     */
    public function getTopicId($unit_id) {
    	if(!$unit_id || !is_numeric($unit_id)) {
            return $this->error('id must be integer');
        }

        $topic = DB::table('topic')
            ->join('unit', 'topic.unit_id', '=', 'unit.id')
			->where('unit.id', $unit_id)
            ->select('topic.id')
            ->first();

        $topic_id = $topic->id;
        return $this->success($topic_id);
    }
  
    /**
     * Mark a half of unit as done.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function doneHalfUnit(Request $request) {
    	$unit_id = $request->unit_id;
        $student = JWTAuth::parseToken()->authenticate();

        $topics = DB::table('topic')
            ->join('unit', 'topic.unit_id', '=', 'unit.id')
			->where('unit.id', $unit_id)
            ->select('topic.id')
            ->get();
        $middleTopicIndex = round(count($topics)/2);

		for ($i = 0; $i < $middleTopicIndex-1; $i++) {
			$topicId = $topics[$i]->id;
			DB::table('progresses')->insert([
			    'student_id' => $student->id, 
			    'entity_id' => $topicId,
			    'entity_type' => 1
			]);
			$lessons = DB::table('lesson')
            	->join('topic', 'lesson.topic_id', '=', 'topic.id')
				->where('topic.id', '=', $topicId)
            	->select('lesson.id')
            	->get();
			foreach ($lessons as $lesson) {
				DB::table('progresses')->insert([
				    'student_id' => $student->id, 
				    'entity_id' => $lesson->id,
				    'entity_type' => 0
				]);
			}
		}

    	$topic_id = $topics[$middleTopicIndex]->id;
        return $this->success($topic_id);
    }

    /**
     * Mark unit as done.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function doneUnit(Request $request) {
    	$unit_id = $request->unit_id;
    	$student = JWTAuth::parseToken()->authenticate();
		
    	// done lessons
		$lessons = DB::table('lesson')
            ->join('topic', 'lesson.topic_id', '=', 'topic.id')
            ->join('unit', 'topic.unit_id', '=', 'unit.id')
			->where('unit.id', $unit_id)
            ->select('lesson.id')
            ->get();
		foreach ($lessons as $lesson) {
			DB::table('progresses')->insert([
			    'student_id' => $student->id, 
			    'entity_id' => $lesson->id,
			    'entity_type' => 0
			]);
		}
		// done topics
		$topics = DB::table('topic')
            ->join('unit', 'topic.unit_id', '=', 'unit.id')
			->where('unit.id', $unit_id)
            ->select('topic.id')
            ->get();
        foreach ($topics as $topic) {
			DB::table('progresses')->insert([
			    'student_id' => $student->id, 
			    'entity_id' => $topic->id,
			    'entity_type' => 1
			]);
		}
		// done unit
		DB::table('progresses')->insert([
		    'student_id' => $student->id, 
		    'entity_id' => $unit_id,
		    'entity_type' => 2
		]);
		
    	return $this->success("Unit ".$unit_id." done!");
    }
}