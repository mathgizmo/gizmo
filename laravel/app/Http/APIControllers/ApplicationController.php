<?php

namespace App\Http\APIControllers;

use App\Application;
use App\ClassApplication;
use App\Question;
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
        return $this->success([
            'items' => array_values(Application::where('teacher_id', $this->user->id)
                ->where('type', 'test')->get()->toArray())
        ]);
    }

    public function startTest(Request $request, $test_id) {
        $test = ClassApplication::where('id', $test_id)->first();
        if (!$test) {
            abort(404, 'Test not found!');
        }
        $class_app_stud = DB::table('classes_applications_students')
            ->where('class_app_id', $test->id)
            ->where('student_id', $this->user->id)->first();
        if ($class_app_stud && $class_app_stud->end_at) {
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
        if ($test->password && (!$class_app_stud || !$class_app_stud->is_revealed)) {
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
            ->where('student_id', $this->user->id)->first();
        if ($class_student && $class_student->test_duration_multiply_by != 1) {
            $duration = $test->duration && $class_student
                ? ($test->duration * $class_student->test_duration_multiply_by)
                : ($test->duration ?: null);
            $test_resource['duration'] = $duration;
        }
        $app = $test->application ?: null;
        $test_resource['name'] = $app ? $app->name : $test->app_id;
        $test_resource['allow_any_order'] = $app ? ($app->allow_any_order ? true : false) : false;
        $questions = [];
        if ($class_app_stud && $class_app_stud->start_at) {
            $stud_questions = DB::table('students_test_questions')
                ->where('class_app_id', $test_id)
                ->where('student_id', $this->user->id)
                ->where('is_answered', false)
                ->get();
            foreach ($stud_questions as $stud_question) {
                $question = Question::with('answers')->where('id', $stud_question->question_id)->first();
                $questions[] = $question;
            }
            $test_resource['questions'] = $questions;
            $questions_count = $class_app_stud->questions_count;
            $test_resource['questions_count'] = $questions_count;
            $time_left = $test_resource['duration'] - Carbon::now()->diffInSeconds(Carbon::parse($class_app_stud->start_at));
            $test_resource['time_left'] = $time_left;
        } else {
            $lesson_ids = $app->getLessonsQuery()->orderBy('order_no', 'ASC')->orderBy('id', 'ASC')->pluck('id');
            DB::table('students_test_questions')->where('class_app_id', $test_id)
                ->where('student_id', $this->user->id)->delete();
            foreach ($lesson_ids as $lesson_id) {
                $question = Question::with('answers')
                    ->where('lesson_id', $lesson_id)
                    ->inRandomOrder()->first();
                if ($question) {
                    $questions[] = $question;
                    $lesson = $question->lesson;
                    $topic = $lesson ? $lesson->topic : null;
                    $unit = $topic ? $topic->unit : null;
                    DB::table('students_test_questions')->insert([
                        'class_app_id' => $test_id,
                        'student_id' => $this->user->id,
                        'question_id' => $question->id,
                        'topic_id' => $lesson ? $lesson->topic_id : null,
                        'unit_id' => $topic ? $topic->unit_id : null,
                        'level_id' => $unit ? $unit->level_id : null,
                        'is_answered' => false
                    ]);
                }
            }
            $test_resource['questions'] = $questions;
            $questions_count = count($test_resource['questions']);
            $test_resource['questions_count'] = $questions_count;
            $time_left = $test_resource['duration'];
            $test_resource['time_left'] = $time_left;
        }
        if (!$class_app_stud) {
            if ($test->is_for_selected_students) {
                return $this->error('The user dont have access to this test!', 400);
            }
            DB::table('classes_applications_students')->insert([
                'class_app_id' => $test->id,
                'student_id' => $this->user->id,
                'start_at' => Carbon::now()->toDateTimeString(),
                'questions_count' => $questions_count
            ]);
        } else if (!$class_app_stud->start_at || !$class_app_stud->questions_count) {
            DB::table('classes_applications_students')->where('class_app_id', $test->id)
                ->where('student_id', $this->user->id)->update([
                    'start_at' => Carbon::now()->toDateTimeString(),
                    'end_at' => null,
                    'mark' => null,
                    'questions_count' => $test_resource['questions_count']
                ]);
        }
        /* DB::table('students_tracking_questions')->where('class_app_id', $test_id)
            ->where('student_id', $this->user->id)->delete(); */
        return $this->success([
            'test' => $test_resource
        ], 200);
    }

    public function finishTest(Request $request, $test_id) {
        $test = ClassApplication::where('id', $test_id)->first();
        if (!$test) {
            abort(404, 'Test not found!');
        }
        $answers_statistics = DB::table('students_test_questions')->select(
            DB::raw("SUM(1) as total"),
            DB::raw("SUM(IF(is_right_answer, 1, 0)) as complete")
        )->where('class_app_id', $test_id)->where('student_id', $this->user->id)->first();
        $correct_question_rate = $answers_statistics->total > 0
            ? $answers_statistics->complete / $answers_statistics->total : 1;
        DB::table('classes_applications_students')->where('class_app_id', $test->id)
            ->where('student_id', $this->user->id)->update([
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
            $app->allow_any_order = request('allow_any_order') ?: null;
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
            $app->duration = request('duration') ?: null;
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
                $app->allow_any_order = request('allow_any_order') ?: null;
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
                $app->duration = request('duration') ?: null;
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

}
