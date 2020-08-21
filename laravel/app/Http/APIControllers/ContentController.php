<?php

namespace App\Http\APIControllers;

use App\Lesson;
use App\Level;
use App\Student;
use App\Topic;
use App\Unit;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class ContentController extends Controller
{

    private $student;

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
    }

    public function index()
    {
        $levels = Level::where('dev_mode', 0)->get();
        foreach ($levels as $level) {
            $units = Unit::where('level_id', $level->id)->where('dev_mode', 0)->get();
            foreach ($units as $unit) {
                $topics = Topic::where('unit_id', $unit->id)->where('dev_mode', 0)->get();
                foreach ($topics as $topic) {
                    try {
                        if($topic->icon_src == '' || !file_exists('../admin/'.$topic->icon_src)) {
                            $topic->icon_src = 'images/default-icon.svg';
                        }
                    } catch (\Exception $e) {
                        $topic->icon_src = 'images/default-icon.svg';
                    }
                    $topic->lessons = Lesson::where('topic_id', $topic->id)->where('dev_mode', 0)->get();
                }
                $unit->topics = $topics;
            }
            $level->units = $units;
        }
        return $this->success($levels);
    }

}
