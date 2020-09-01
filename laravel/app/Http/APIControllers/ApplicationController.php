<?php

namespace App\Http\APIControllers;

use App\Application;
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

    public function all() {
        return $this->success([
            'items' => array_values(Application::where('teacher_id', $this->user->id)->get()->toArray())
        ]);
    }

    public function store() {
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
            $app->testout_attempts = request('testout_attempts') ?: -1;
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
                $app->testout_attempts = request('testout_attempts') ?: -1;
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
