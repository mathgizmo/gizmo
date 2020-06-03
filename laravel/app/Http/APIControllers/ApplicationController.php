<?php

namespace App\Http\APIControllers;

use App\Application;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use JWTAuth;

class ApplicationController extends Controller
{

	private $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function all() {
        return $this->success([
            'items' => array_values(Application::where('teacher_id', $this->user->id)->get()->toArray())
        ]);
    }

    public function store() {
        try {
            $app = new Application();
            $app->name = request('name');
            if (request('icon')) {
                $app->icon = request('icon');
            }
            if (request('due_date')) {
                $app->due_date = request('due_date');
            }
            $app->teacher_id = $this->user->id;
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
            $app = Application::where('id', $app_id)->where('teacher_id', $this->user->id)->first();
            if ($app) {
                if (request()->has('name')) {
                    $app->name = request('name');
                }
                if (request()->has('subscription_type')) {
                    $app->icon = request('icon');
                }
                if (request()->has('invitations')) {
                    $app->due_date = request('due_date');
                }
                $app->save();
                parse_str(request('tree'), $tree);
                $app->updateTree($tree);
                return $this->success(['item' => $app]);
            }
        } catch (\Exception $e) {}
        return $this->error('Error.');
    }

    public function delete($app_id) {
        $app = Application::where('id', $app_id)->where('teacher_id', $this->user->id)->first();
        if ($app) {
            $app->deleteTree();
            $app->delete();
            return $this->success('Ok.');
        }
        return $this->error('Error.');
    }

    public function getAppTree($app_id) {
        $app = Application::where('id', $app_id)->where('teacher_id', $this->user->id)->first();
        if (!$app) {
            $app = new Application();
        }
        return $this->success(['items' => $app->getTree()]);
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
