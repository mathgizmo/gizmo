<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Application extends Model
{
    protected $table = 'applications';

    // protected $fillable = ['id', 'name'];

    public function students() {
        return $this->hasMany('App\Student', 'app_id', 'id');
    }

    public function levels() {
        return $this->belongsToMany('App\Level', 'application_has_models', 'app_id', 'model_id')->where('model_type', 'level');
    }

    public function units() {
        return $this->belongsToMany('App\Unit', 'application_has_models', 'app_id', 'model_id')->where('model_type', 'unit');
    }

    public function topics() {
        return $this->belongsToMany('App\Topic', 'application_has_models', 'app_id', 'model_id')->where('model_type', 'topic');
    }

    public function lessons() {
        return $this->belongsToMany('App\Lesson', 'application_has_models', 'app_id', 'model_id')->where('model_type', 'lesson');
    }

    public function icon() {
        if ($this->icon) {
            return $this->icon;
        }
        return '/images/default-icon.svg';
    }

    public function getTree() {
        $levels = $this->levels()->get()->keyBy('id');
        $units = $this->units()->get()->keyBy('id');
        $topics = $this->topics()->get()->keyBy('id');
        $lessons = $this->lessons()->get()->keyBy('id');
        $tree = [];
        foreach (Level::all() as $level) {
            $unit_items = [];
            $level_is_collapsed = true;
            foreach ($level->units()->get() as $unit) {
                $topic_items = [];
                $unit_is_collapsed = true;
                foreach ($unit->topics()->get() as $topic) {
                    $lesson_items = [];
                    $topic_is_collapsed = true;
                    foreach ($topic->lessons()->get() as $lesson) {
                        $lesson_checked = ($lessons->where('id', $lesson->id)->first()) ? true : false;
                        if ($lesson_checked) {
                            $topic_is_collapsed = $unit_is_collapsed = $level_is_collapsed = false;

                        }
                        array_push($lesson_items, (object) [
                            'id' => $lesson->id,
                            'text' => trim($lesson->title),
                            'checked' => $lesson_checked
                        ]);
                    }
                    $topic_checked = ($topics->where('id', $topic->id)->first()) ? true : false;
                    if ($topic_checked) {
                        $unit_is_collapsed = $level_is_collapsed = false;
                    }
                    array_push($topic_items, (object) [
                        'id' => $topic->id,
                        'text' => trim($topic->title),
                        'children' => $lesson_items,
                        'checked' => $topic_checked,
                        'collapsed' => $topic_is_collapsed
                    ]);
                }
                $unit_checked =($units->where('id', $unit->id)->first()) ? true : false;
                if ($unit_checked) {
                    $level_is_collapsed = false;
                }
                array_push($unit_items, (object) [
                    'id' => $unit->id,
                    'text' => trim($unit->title),
                    'children' => $topic_items,
                    'checked' => $unit_checked,
                    'collapsed' => $unit_is_collapsed
                ]);
            }
            array_push($tree, (object) [
                'id' => $level->id,
                'text' => trim($level->title),
                'children' => $unit_items,
                'checked' => ($levels->where('id', $level->id)->first()) ? true : false,
                'collapsed' => $level_is_collapsed
            ]);
        }
        return $tree;
    }

    public function updateTree($request) {
        DB::table('application_has_models')->where('app_id', $this->id)->delete();
        if (is_array($request['level'])) {
            foreach ($request['level'] as $key => $value) {
                DB::table('application_has_models')->insert(
                    ['app_id' => $this->id, 'model_type' => 'level', 'model_id' => $key]
                );
            }
        }
        if (is_array($request['unit'])) {
            foreach ($request['unit'] as $key => $value) {
                $unit = Unit::where('id', $key)->first();
                if (!$unit || DB::table('application_has_models')->where('model_type', 'level')->where('model_id', $unit->level_id)->count() > 0) {
                    continue;
                }
                DB::table('application_has_models')->insert(
                    ['app_id' => $this->id, 'model_type' => 'unit', 'model_id' => $key]
                );
            }
        }
        if (is_array($request['topic'])) {
            foreach ($request['topic'] as $key => $value) {
                $topic = Topic::where('id', $key)->first();
                if (!$topic || !$topic->unit ||
                    DB::table('application_has_models')->where(function ($query) use ($topic) {
                        $query->where('model_type', 'unit')->where('model_id', $topic->unit_id);
                    })->orWhere(function ($query) use ($topic) {
                        $query->where('model_type', 'level')->where('model_id', $topic->unit->level_id);
                    })->count() > 0) {
                    continue;
                }
                DB::table('application_has_models')->insert(
                    ['app_id' => $this->id, 'model_type' => 'topic', 'model_id' => $key]
                );
            }
        }
        if (is_array($request['lesson'])) {
            foreach ($request['lesson'] as $key => $value) {
                $lesson = Lesson::where('id', $key)->first();
                if (!$lesson || !$lesson->topic || !$lesson->topic->unit ||
                    DB::table('application_has_models')->where(function ($query) use ($lesson) {
                        $query->where('model_type', 'topic')->where('model_id', $lesson->topic_id);
                    })->orWhere(function ($query) use ($lesson) {
                        $query->where('model_type', 'unit')->where('model_id', $lesson->topic->unit_id);
                    })->orWhere(function ($query) use ($lesson) {
                        $query->where('model_type', 'level')->where('model_id', $lesson->topic->unit->level_id);
                    })->count() > 0) {
                    continue;
                }
                DB::table('application_has_models')->insert(
                    ['app_id' => $this->id, 'model_type' => 'lesson', 'model_id' => $key]
                );
            }
        }
    }

    public function deleteTree() {
        DB::table('application_has_models')->where('app_id', $this->id)->delete();
    }

    public function getLevels() {
        // DB::select('select * from level where dev_mode = 0 order by order_no, id asc'); // all levels
        $app_id = $this->id;
        $items = DB::table('level')->where(function ($query) use($app_id) {
            $query->whereIn('id', function($q1) use($app_id) {
                $q1->select('model_id')->from('application_has_models')->where('model_type', 'level')->where('app_id', $app_id);
            })->orWhereIn('id', function($q2) use($app_id) {
                $q2->select('level_id')->from('unit')->whereIn('id', function($q3) use($app_id) {
                    $q3->select('model_id')->from('application_has_models')->where('model_type', 'unit')->where('app_id', $app_id);
                });
            })->orWhereIn('id', function($q4) use($app_id) {
                $q4->select('level_id')->from('unit')->whereIn('id', function($q5) use($app_id) {
                    $q5->select('unit_id')->from('topic')->whereIn('id', function($q6) use($app_id) {
                        $q6->select('model_id')->from('application_has_models')->where('model_type', 'topic')->where('app_id', $app_id);
                    });
                });
            })->orWhereIn('id', function($q7) use($app_id) {
                $q7->select('level_id')->from('unit')->whereIn('id', function($q8) use($app_id) {
                    $q8->select('unit_id')->from('topic')->whereIn('id', function($q9) use($app_id) {
                        $q9->select('topic_id')->from('lesson')->whereIn('id', function($q10) use($app_id) {
                            $q10->select('model_id')->from('application_has_models')->where('model_type', 'lesson')->where('app_id', $app_id);
                        });
                    });
                });
            });
        })->where('dev_mode', 0)->orderBy('order_no', 'ASC')->orderBy('id', 'ASC')->get();
        return $items;
    }

    public function getUnits($level_id = null) {
        // DB::select('select * from unit where dev_mode = 0 order by order_no, id asc'); // all units
        $app_id = $this->id;
        $items = DB::table('unit')->where(function ($query) use($app_id) {
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
            })->orWhereIn('id', function($q7) use($app_id) {
                $q7->select('id')->from('unit')->whereIn('level_id', function($q8) use($app_id) {
                    $q8->select('model_id')->from('application_has_models')->where('model_type', 'level')->where('app_id', $app_id);
                });
            });
        });
        if ($level_id) {
            $items->where('level_id', $level_id);
        }
        return $items->where('dev_mode', 0)->orderBy('order_no', 'ASC')->orderBy('id', 'ASC')->get();
    }

    public function getTopics($unit_id = null) {
        // DB::select('select * from topic where dev_mode = 0 order by order_no, id asc'); // all topics
        $app_id = $this->id;
        $items = DB::table('topic')->where(function ($query) use($app_id) {
            $query->whereIn('id', function($q1) use($app_id) {
                $q1->select('model_id')->from('application_has_models')->where('model_type', 'topic')->where('app_id', $app_id);
            })->orWhereIn('id', function($q2) use($app_id) {
                $q2->select('topic_id')->from('lesson')->whereIn('id', function($q3) use($app_id) {
                    $q3->select('model_id')->from('application_has_models')->where('model_type', 'lesson')->where('app_id', $app_id);
                });
            })->orWhereIn('id', function($q4) use($app_id) {
                $q4->select('id')->from('topic')->whereIn('unit_id', function($q5) use($app_id) {
                    $q5->select('model_id')->from('application_has_models')->where('model_type', 'unit')->where('app_id', $app_id);
                });
            })->orWhereIn('id', function($q6) use($app_id) {
                $q6->select('id')->from('topic')->whereIn('unit_id', function($q7) use($app_id) {
                    $q7->select('id')->from('unit')->whereIn('level_id', function($q8) use($app_id) {
                        $q8->select('model_id')->from('application_has_models')->where('model_type', 'level')->where('app_id', $app_id);
                    });
                });
            });
        });
        if ($unit_id) {
            $items->where('unit_id', $unit_id);
        }
        return $items->where('dev_mode', 0)->orderBy('order_no', 'ASC')->orderBy('id', 'ASC')->get();
    }

    public function getLessons($topic_id = null, $is_admin = false) {
        // get lessons without $topic_id is NOT IMPLEMENTED!
        $app_id = $this->id;
        $query = DB::table('lesson');
        if (!$is_admin) {
            $query->where('dev_mode', 0);
        }
        if ($topic_id) {
            $lessons = DB::table('lesson');
            if (!$is_admin) {
                $lessons->where('dev_mode', 0);
            }
            $lessons->whereIn('id', function($q) use($app_id) {
                $q->select('model_id')->from('application_has_models')->where('model_type', 'lesson')->where('app_id', $app_id);
            })->where('topic_id', $topic_id);
            if ($lessons->count() > 0) {
                return $lessons->orderBy('order_no')->orderBy('id')->get();
            }
            $query->where('topic_id', $topic_id);
        }
        return $query->orderBy('order_no')->orderBy('id')->get();
    }

    public $timestamps = false;
}
