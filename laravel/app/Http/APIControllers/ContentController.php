<?php

namespace App\Http\APIControllers;

use App\Lesson;
use App\Level;
use App\Topic;
use App\Unit;

class ContentController extends Controller
{

    public function index()
    {
        $levels = Level::where('dev_mode', 0)->orderBy('order_no', 'ASC')->get();
        foreach ($levels as $level) {
            // Eager-load units for the level
            $units = Unit::where('level_id', $level->id)
                ->where('dev_mode', 0)
                ->orderBy('order_no', 'ASC')
                ->get();
            foreach ($units as $unit) {
                $topics = Topic::where('unit_id', $unit->id)->where('dev_mode', 0)->orderBy('order_no', 'ASC')->get();
                foreach ($topics as $topic) {
                    try {
                        if($topic->icon_src == '' || !file_exists('../admin/'.$topic->icon_src)) {
                            $topic->icon_src = 'images/default-icon.svg';
                        }
                    } catch (\Exception $e) {
                        $topic->icon_src = 'images/default-icon.svg';
                    }
                    $topic->lessons = Lesson::where('topic_id', $topic->id)->where('dev_mode', 0)->orderBy('order_no', 'ASC')->get();
                }
                $unit->topics = $topics;
                // Attach tag IDs for frontend filtering
                try {
                    // Using relation to fetch tag IDs associated with the unit (Tag table is 'tag')
                    $unit->tag_ids = $unit->tags()->pluck('tag.id')->values();
                } catch (\Exception $e) {
                    $unit->tag_ids = [];
                }
            }
            $level->units = $units;
        }
        return $this->success($levels);
    }

}
