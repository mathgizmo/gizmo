<?php

namespace App\Http\APIControllers;

use App\Application;
use App\ClassApplication;
use App\ClassApplicationStudent;
use App\Lesson;
use App\Question;
use App\Topic;
use App\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApplicationController extends Controller
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

    public function getAssignments() {
        return $this->success([
            'items' => array_values(Application::where('teacher_id', $this->user->id)
                ->where('type', 'assignment')->get()->toArray())
        ]);
    }

    public function getTests() {
        $items = Application::where('teacher_id', $this->user->id)->where('type', 'test')->get();
        foreach ($items as $item) {
            $item->duration = $item->duration ? round($item->duration / 60) : 0; // seconds to minutes
            $item->total_questions_count = $item->getQuestionsCount();
        }
        return $this->success([
            'items' => array_values($items->toArray())
        ]);
    }

    public function startTest(Request $request, $test_id) {
        try {
            $test = ClassApplication::where('id', $test_id)->first();
            if (!$test) { abort(404, 'Test not found!'); }
            $test_student = ClassApplicationStudent::where('class_app_id', $test->id)
                ->where('student_id', $this->user->id)->first();
            if ($test->is_for_selected_students && !$test_student) {
                return $this->error('The user dont have access to this test!', 400);
            }
            $attempts = $test_student ? DB::table('students_test_attempts')
                ->where('test_student_id', $test_student->id)
                ->get() : [];
            $attempts_count = count($attempts);
            if ($attempts_count > $test->attempts) {
                return $this->error('You already finished this test!', 410);
            }
            if ($test->start_date || $test->due_date) {
                $now = \Illuminate\Support\Carbon::now()->toDateTimeString();
                if ($test->start_date) {
                    $start_at = $test->start_time ? $test->start_date.' '.$test->start_time : $test->start_date.' 00:00:00';
                } else {
                    $start_at = null;
                }
                if ($test->due_date) {
                    $due_at = $test->due_time ? $test->due_date.' '.$test->due_time : $test->due_date.' 00:00:00';
                } else {
                    $due_at = null;
                }
                $is_blocked = ($start_at && $now < $start_at) || ($due_at && $now > $due_at);
                if ($is_blocked) {
                    return $this->error('This test in unavailable at the moment!', 400);
                }
            }
            if (!$test_student) {
                $test_student = new ClassApplicationStudent();
                $test_student->class_app_id = $test->id;
                $test_student->student_id = $this->user->id;
                $test_student->save();
            }
            if ($test->password && !$test_student->is_revealed) {
                return $this->error('The user dont have access to this test!', 400);
            }
            $test_resource = [
                'id' => $test->id,
                'class_id' => $test->class_id,
                'app_id' => $test->app_id,
                'start_date' => $test->start_date,
                'start_time' => $test->start_time,
                'due_date' => $test->due_date,
                'due_time' => $test->due_time,
                'duration' => $test->duration,
                'has_password' => $test->password ? true : false,
            ];
            $class_student = DB::table('classes_students')
                ->where('class_id', $test->class_id)
                ->where('student_id', $this->user->id)
                ->first();
            if ($class_student && $class_student->test_duration_multiply_by != 1) {
                $duration = $test->duration && $class_student
                    ? ($test->duration * $class_student->test_duration_multiply_by)
                    : ($test->duration ?: null);
                $test_resource['duration'] = $duration;
            }
            $app = $test->application ?: null;
            $test_resource['name'] = $app ? $app->name : $test->app_id;
            $test_resource['allow_any_order'] = $app ? ($app->allow_any_order ? true : false) : false;
            $test_resource['allow_back_tracking'] = $app ? ($app->allow_back_tracking ? true : false) : false;
            $questions = [];
            $current_attempt = $attempts_count > 0
                ? $attempts->whereNull('end_at')->first()
                : null;
            if ($current_attempt) {
                $stud_questions = DB::table('students_test_questions')
                    ->where('class_app_id', $test_id)
                    ->where('attempt_id', $current_attempt->id)
                    ->where('student_id', $this->user->id)
                    ->where('is_answered', false)
                    ->orderBy('order_no', 'ASC')
                    ->get();
                foreach ($stud_questions as $stud_question) {
                    $question = Question::with('answers')->where('id', $stud_question->question_id)->first();
                    $question->order_no = $stud_question->order_no;
                    $questions[] = $question;
                }
                $test_resource['questions'] = $questions;
                $test_resource['questions_count'] = $current_attempt->questions_count;
                $time_left = $test_resource['duration'] - Carbon::now()->diffInSeconds(Carbon::parse($current_attempt->start_at));
                $test_resource['time_left'] = $time_left;
            } else {
                $attempt_id = DB::table('students_test_attempts')->insertGetId([
                    'attempt_no' => $test_student->attempts_count + $test_student->resets_count + 1,
                    'test_student_id' => $test_student->id,
                    'start_at' => Carbon::now()->toDateTimeString(),
                ]);
                $lesson_ids = $app->getLessonsQuery()
                    ->join('topic', 'topic.id', '=', 'lesson.topic_id')
                    ->join('unit', 'unit.id', '=', 'topic.unit_id')
                    ->join('level', 'level.id', '=', 'unit.level_id')
                    ->orderBy('level.order_no', 'ASC')
                    ->orderBy('unit.order_no', 'ASC')
                    ->orderBy('topic.order_no', 'ASC')
                    ->orderBy('lesson.order_no', 'ASC')
                    ->select('lesson.id')->pluck('lesson.id');
                $question_order_no = 1;
                foreach ($lesson_ids as $lesson_id) {
                    $questions_list = Question::with('answers')
                        ->where('lesson_id', $lesson_id)
                        ->inRandomOrder()->limit($app->question_num)->get();
                    if ($questions_list && count($questions_list) > 0) {
                        foreach ($questions_list as $question) {
                            $question->order_no = $question_order_no;
                            $questions[] = $question;
                            $lesson = $question->lesson;
                            $topic = $lesson ? $lesson->topic : null;
                            $unit = $topic ? $topic->unit : null;
                            DB::table('students_test_questions')->insert([
                                'attempt_id' => $attempt_id,
                                'class_app_id' => $test_id,
                                'student_id' => $this->user->id,
                                'question_id' => $question->id,
                                'topic_id' => $lesson ? $lesson->topic_id : null,
                                'unit_id' => $topic ? $topic->unit_id : null,
                                'level_id' => $unit ? $unit->level_id : null,
                                'is_answered' => false,
                                'order_no' => $question_order_no
                            ]);
                            $question_order_no++;
                        }
                    }
                }
                $test_resource['questions'] = $questions;
                $questions_count = count($test_resource['questions']);
                $test_resource['questions_count'] = $questions_count;
                $time_left = $test_resource['duration'];
                $test_resource['time_left'] = $time_left;
                DB::table('students_test_attempts')
                    ->where('id', $attempt_id)
                    ->update([
                        'questions_count' => $questions_count
                    ]);
                $test_student->attempts_count += 1;
                $test_student->save();
            }
            return $this->success([
                'test' => $test_resource
            ], 200);
        } catch (\Exception $e) {
            return $this->error($e->getLine(), 500);
        }
    }

    public function finishTest(Request $request, $test_id) {
        $test = ClassApplication::where('id', $test_id)->first();
        if (!$test) {
            abort(404, 'Test not found!');
        }
        $test_student = ClassApplicationStudent::where('class_app_id', $test->id)
            ->where('student_id', $this->user->id)->first();
        $current_attempt = $test_student ?
            DB::table('students_test_attempts')
                ->where('test_student_id', $test_student->id)
                ->whereNull('end_at')
                ->first() : null;
        if (!$current_attempt) {
            abort(404, 'Attempt not found!');
        }
        $answers_statistics = DB::table('students_test_questions')
            ->select(
                DB::raw("SUM(1) as total"),
                DB::raw("SUM(IF(is_right_answer, 1, 0)) as complete")
            )
            ->where('attempt_id', $current_attempt->id) // ->where('class_app_id', $test_id)->where('student_id', $this->user->id)
            ->first();
        $correct_question_rate = $answers_statistics->total > 0
            ? $answers_statistics->complete / $answers_statistics->total : 1;
        DB::table('students_test_attempts')
            ->where('id', $current_attempt->id)
            ->update([
                'mark' => $correct_question_rate,
                'end_at' => Carbon::now()->toDateTimeString()
            ]);
        return $this->success([
            'correct_question_rate' => $correct_question_rate
        ], 200);
    }

    public function storeAssignment(Request $request) {
        return $this->store($request, 'assignment');
    }

    public function storeTest(Request $request) {
        return $this->store($request, 'test');
    }

    private function store(Request $request, $type = 'assignment') {
        try {
            $validator = Validator::make(request()->all(), [ 'name' => 'required|max:255' ]);
            if ($validator->fails()) {
                return $this->error($validator->messages());
            }
            $app = new Application();
            $app->name = request('name');
            if (request('icon')) {
                $app->icon = request('icon');
            }
            $app->teacher_id = $this->user->id;
            $app->allow_any_order = request('allow_any_order') ? true : false;
            $app->allow_back_tracking = request('allow_back_tracking') ? true : false;
            $app->testout_attempts = request('testout_attempts') >= -1 ? intval(request('testout_attempts')) : 0;
            if (request()->has('question_num')) {
                $question_num = request('question_num');
                if (is_numeric($question_num)) {
                    if ($question_num < 0) {
                        $question_num = 0;
                    }
                    $app->question_num = (int) $question_num;
                }
            }
            $app->duration = request('duration') ? request('duration') * 60 : null; // minutes to seconds
            $app->type = $type;
            $app->save();
            parse_str(request('tree'), $tree);
            $app->updateTree($tree);
            return $this->success(['item' => $app]);
        } catch (\Exception $e) {
            return $this->error('Error.');
        }
    }

    public function update($app_id) {
        try {
            $validator = Validator::make(request()->all(), ['name' => 'required|max:255']);
            if ($validator->fails()) {
                return $this->error($validator->messages());
            }
            $app = Application::where('id', $app_id)->where('teacher_id', $this->user->id)->first();
            if ($app) {
                if (request()->has('name')) {
                    $app->name = request('name');
                }
                if (request('icon')) {
                    $app->icon = request('icon');
                }
                $app->allow_any_order = request('allow_any_order') ? true : false;
                $app->allow_back_tracking = request('allow_back_tracking') ? true : false;
                $app->testout_attempts = request('testout_attempts') >= -1 ? intval(request('testout_attempts')) : 0;
                if (request()->has('question_num')) {
                    $question_num = request('question_num');
                    if (is_numeric($question_num)) {
                        if ($question_num < 0) {
                            $question_num = 0;
                        }
                        $app->question_num = (int) $question_num;
                    }
                }
                $app->duration = request('duration') ? request('duration') * 60 : null; // minutes to seconds
                $app->save();
                parse_str(request('tree'), $tree);
                $success = $app->updateTree($tree);
                return $this->success(['item' => $app, 'success' => $success]);
            }
        } catch (\Exception $e) {}
        return $this->error('Error.');
    }

    public function delete($app_id) {
        $app = Application::where('id', $app_id)->where('teacher_id', $this->user->id)->first();
        if ($app) {
            $app->deleteTree();
            $app->delete();
            DB::table('classes_applications')->where('app_id', $app_id)->delete();
            return $this->success('Ok.');
        }
        return $this->error('Error.');
    }

    public function copy($app_id) {
        $app = Application::where('id', $app_id)->where('teacher_id', $this->user->id)->first();
        if ($app) {
            $new_app = $app->replicateWithRelations();
            return $this->success(['item' => $new_app, 'success' => true]);
        }
        return $this->error('Not Found!', 404);
    }

    public function getAppTree($app_id) {
        $app = Application::where('id', $app_id)->where('teacher_id', $this->user->id)->first();
        if (!$app) {
            $app = new Application();
        }
        return $this->success(['items' => $app->getTree(true)]);
    }

    public function getAvailableIcons() {
        if (!$this->user->isTeacher() && !$this->user->isAdmin()) {
            abort('403', 'Unauthorized!');
        }
        $icons = array();
        foreach (glob("../admin/images/icons/*.svg") as $icon) {
            $icons[] = str_replace('../admin/', '', $icon);
        }
        return $this->success(['items' => array_values($icons)]);
    }

    public function getQuestionsCount(Request $request) {
        parse_str(request('tree'), $tree);
        $questions_per_lesson = request('questions_per_lesson') ?: 1;
        $levels = [];
        $units = [];
        $topics = [];
        $lessons = [];
        try {
            if ((is_array($tree) ? array_key_exists('level', $tree) : $tree['level']) && is_array($tree['level'])) {
                foreach ($tree['level'] as $key => $value) {
                    array_push($levels, $key);
                }
            }
            if ((is_array($tree) ? array_key_exists('unit', $tree) : $tree['unit']) && is_array($tree['unit'])) {
                foreach ($tree['unit'] as $key => $value) {
                    $unit = Unit::where('id', $key)->first();
                    if (!$unit || in_array($unit->level_id, $levels)) {
                        continue;
                    }
                    array_push($units, $key);
                }
            }
            if ((is_array($tree) ? array_key_exists('topic', $tree) : $tree['topic']) && is_array($tree['topic'])) {
                foreach ($tree['topic'] as $key => $value) {
                    $topic = Topic::where('id', $key)->first();
                    $unit = $topic ? $topic->unit : null;
                    if (!$topic || !$unit || in_array($unit->level_id, $levels) || in_array($topic->unit_id, $units)) {
                        continue;
                    }
                    array_push($topics, $key);
                }
            }
            if ((is_array($tree) ? array_key_exists('lesson', $tree) : $tree['lesson']) && is_array($tree['lesson'])) {
                foreach ($tree['lesson'] as $key => $value) {
                    $lesson = Lesson::where('id', $key)->first();
                    $topic = $lesson ? $lesson->topic : null;
                    $unit = $topic ? $topic->unit : null;
                    if (!$lesson || !$topic || !$unit || in_array($unit->level_id, $levels)
                        || in_array($topic->unit_id, $units) || in_array($lesson->topic_id, $topics)) {
                        continue;
                    }
                    array_push($lessons, $key);
                }
            }
        } catch (\Exception $e) { }
        $questions_count = 0;
        $lesson_ids = DB::table('lesson')
            ->where(function ($q1) use($levels, $units, $topics, $lessons) {
                $q1->whereIn('lesson.id', $lessons)->orWhereIn('lesson.id', function($q3) use($topics) {
                    $q3->select('lesson.id')->from('lesson')->whereIn('topic_id', $topics);
                })->orWhereIn('lesson.id', function($q5) use($units) {
                    $q5->select('lesson.id')->from('lesson')->whereIn('topic_id', function($q6) use($units) {
                        $q6->select('topic.id')->from('topic')->whereIn('unit_id', $units);
                    });
                })->orWhereIn('lesson.id', function($q8) use($levels) {
                    $q8->select('lesson.id')->from('lesson')->whereIn('topic_id', function($q9) use($levels) {
                        $q9->select('topic.id')->from('topic')->whereIn('unit_id', function($q10) use($levels) {
                            $q10->select('unit.id')->from('unit')->whereIn('level_id', $levels);
                        });
                    });
                });
            })
            ->where('lesson.dev_mode', 0)
            ->select('lesson.id')->pluck('lesson.id');
        foreach ($lesson_ids as $lesson_id) {
            try {
                $questions_count += count(Question::where('lesson_id', $lesson_id)
                    ->limit($questions_per_lesson)->select('id')->pluck('id'));
            } catch (\Exception $e) { }
        }
        return $this->success(['questions_count' => $questions_count, 'success' => true]);
    }

}
