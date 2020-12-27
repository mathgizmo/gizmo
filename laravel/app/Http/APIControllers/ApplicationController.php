<?php

namespace App\Http\APIControllers;

use App\Application;
use App\ClassApplication;
use App\Http\Resources\TestResource;
use App\Progress;
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
        $class_app_stud = DB::table('classes_applications_students')->where('class_app_id', $test->id)
            ->where('student_id', $this->user->id)->first();
        if ($class_app_stud && $class_app_stud->mark) {
            return $this->error('You already finished this test!', 410);
        }
        $exists = DB::table('classes_applications_students')->where('class_app_id', $test->id)
                ->where('student_id', $this->user->id)->count() > 0;
        if (!$exists) {
            if ($test->is_for_selected_students) {
                return $this->error('The user dont have access to this test!', 400);
            }
            DB::table('classes_applications_students')->insert([
                'class_app_id' => $test->id,
                'student_id' => $this->user->id,
                'start_at' => Carbon::now()->toDateTimeString(),
                'questions_count' => count($test->application->getLessonsQuery()->pluck('id'))
            ]);
        } else {
            DB::table('classes_applications_students')->where('class_app_id', $test->id)
                ->where('student_id', $this->user->id)->update([
                    'start_at' => Carbon::now()->toDateTimeString(),
                    'end_at' => null,
                    'mark' => null,
                    'questions_count' => count($test->application->getLessonsQuery()->pluck('id'))
                ]);
        }
        DB::table('students_tracking_questions')->where('class_app_id', $test_id)
            ->where('student_id', $this->user->id)->delete();
        return $this->success([
            'test' => new TestResource($test, $this->user)
        ], 200);
    }

    public function finishTest(Request $request, $test_id) {
        $test = ClassApplication::where('id', $test_id)->first();
        if (!$test) {
            abort(404, 'Test not found!');
        }
        DB::table('classes_applications_students')->where('class_app_id', $test->id)
            ->where('student_id', $this->user->id)->update([
                'end_at' => Carbon::now()->toDateTimeString()
            ]);
        $tracking_questions_statistics = DB::table('students_tracking_questions')->select(
            // DB::raw("SUM(1) as total"),
            DB::raw("SUM(IF(is_right_answer, 1, 0)) as complete")
        )->where('class_app_id', $test_id)->where('student_id', $this->user->id)->first();
        $class_app_stud = DB::table('classes_applications_students')->where('class_app_id', $test->id)
            ->where('student_id', $this->user->id)->first();
        $correct_question_rate = $class_app_stud && $class_app_stud->questions_count
            ? $tracking_questions_statistics->complete / $class_app_stud->questions_count : 1;
        DB::table('classes_applications_students')->where('class_app_id', $test->id)
            ->where('student_id', $this->user->id)->update([
                'mark' => $correct_question_rate
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
