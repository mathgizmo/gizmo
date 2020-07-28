<?php

namespace App\Http\APIControllers;

use App\PlacementQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Unit;

class PlacementController extends Controller
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

    public function get()
    {
        $placement = PlacementQuestion::all();
        return $this->success($placement);
    }

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

    public function doneHalfUnit(Request $request) {
        $unit_id = $request['unit_id'];
        $student = $this->user;
        $topics = DB::table('topic')
            ->join('unit', 'topic.unit_id', '=', 'unit.id')
            ->where('unit.id', $unit_id)
            ->select('topic.id')
            ->get()->all();
        $middleTopicIndex = round(count($topics)/2);
        for ($i = 0; $i < $middleTopicIndex-1; $i++) {
            $topicId = $topics[$i]->id;
            DB::table('progresses')->insert([
                'student_id' => $student->id,
                'entity_id' => $topicId,
                'entity_type' => 'topic'
            ]);
            $lessons = DB::table('lesson')
                ->join('topic', 'lesson.topic_id', '=', 'topic.id')
                ->where('topic.id', '=', $topicId)
                ->select('lesson.id')
                ->get()->all();
            foreach ($lessons as $lesson) {
                DB::table('progresses')->insert([
                    'student_id' => $student->id,
                    'entity_id' => $lesson->id,
                    'entity_type' => 'lesson'
                ]);
            }
        }
        $topic_id = $topics[$middleTopicIndex]->id;
        return $this->success($topic_id);
    }

    public function doneUnit(Request $request) {
        $unit_id = $request['unit_id'];
        $student = $this->user;
        // done lessons
        $lessons = DB::table('lesson')
            ->join('topic', 'lesson.topic_id', '=', 'topic.id')
            ->join('unit', 'topic.unit_id', '=', 'unit.id')
            ->where('unit.id', $unit_id)
            ->select('lesson.id')
            ->get()->all();
        foreach ($lessons as $lesson) {
            DB::table('progresses')->insert([
                'student_id' => $student->id,
                'entity_id' => $lesson->id,
                'entity_type' => 'lesson'
            ]);
        }
        // done topics
        $topics = DB::table('topic')
            ->join('unit', 'topic.unit_id', '=', 'unit.id')
            ->where('unit.id', $unit_id)
            ->select('topic.id')
            ->get()->all();
        foreach ($topics as $topic) {
            DB::table('progresses')->insert([
                'student_id' => $student->id,
                'entity_id' => $topic->id,
                'entity_type' => 'topic'
            ]);
        }
        // done unit
        DB::table('progresses')->insert([
            'student_id' => $student->id,
            'entity_id' => $unit_id,
            'entity_type' => 'unit'
        ]);

        //find all units from level that are not done yet
        $unit_model = Unit::where("id", $unit_id)->first();
        $units = DB::table('unit')->leftJoin('progresses', function ($join) use ($student) {
            $join->on('progresses.student_id', '=', DB::raw($student->id))
            ->on('progresses.entity_type', '=', DB::raw('"unit"'))
            ->on('progresses.entity_id', '=', 'unit.id');
        })
        ->where(['level_id' => $unit_model->level_id, 'dependency' => 1])
        ->whereNull('progresses.id')->get()->all();
        //if all units are done, mark level as done
        if (!count($units)) {
            // done level
            DB::table('progresses')->insert([
                'student_id' => $student->id,
                'entity_id' => $unit_model->level_id,
                'entity_type' => 'level'
            ]);
        }

        return $this->success("Unit ".$unit_id." done!");
    }
}
