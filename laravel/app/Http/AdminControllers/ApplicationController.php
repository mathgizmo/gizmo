<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Application;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{

    public function __construct()
    {
        // $this->authorizeResource(Application::class); // not working!
    }

    public function index(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $query = Application::query();
        if ($request['id']) {
            $query->where('id', $request['id']);
        }
        if ($request['name']) {
            $query->where('name', 'LIKE', '%' . $request['name'] . '%');
        }
        if ($request['teacher']) {
            $teacher = $request['teacher'];
            $query->whereHas('teacher', function ($q) use ($teacher) {
                $q->where('name', 'LIKE', '%'.$teacher.'%');
            });
        }
        if ($request['sort'] && $request['order']) {
            if ($request['sort'] == 'teacher') {
                $query->leftJoin('students', 'students.id', '=', 'applications.teacher_id')
                    ->orderBy('students.name', request('order'))->select('applications.*');
            } else {
                $query->orderBy($request['sort'], $request['order']);
            }
        }
        return view('applications.index', ['applications' => $query->paginate(10)->appends(request()->query())]);
    }

    public function create()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $icons = array();
        $all = glob("images/icons/*.svg");
        $complete = glob("images/icons/*-gold.svg");
        foreach (array_diff($all, $complete) as $file) {
            $icons[] = $file;
        }
        $tree = (new Application())->getTree();
        return view('applications.create', array(
            'icons' => $icons,
            'tree' => $tree
        ));
    }

    public function store(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
            'name' => 'required',
        ]);
        $application = new Application();
        $application->name = $request['name'];
        if (isset($request['icon']) && $request['icon']) {
            $application->icon = $request['icon'];
        }
        $application->allow_any_order = $request['allow_any_order'] ? true : false;
        $application->testout_attempts = $request['testout_attempts'] >= -1 ? intval($request['testout_attempts']) : 0;
        $application->question_num = $request['question_num'] ?: 3;
        $application->save();
        $application->updateTree($request);
        return redirect('/applications')->with(array('message' => 'Created successfully'));
    }

    public function edit($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $application = Application::find($id);
        $icons = array();
        $all = glob("images/icons/*.svg");
        $complete = glob("images/icons/*-gold.svg");
        foreach (array_diff($all, $complete) as $file) {
            $icons[] = $file;
        }
        $tree = $application->getTree();
        return view('applications.edit', [
            'application' => $application,
            'icons' => $icons,
            'tree' => $tree
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
            'name' => 'required',
        ]);
        $application = Application::where('id', $id)->first();
        if (!$application) {
            return redirect('/applications')->with(array('message' => 'Can\'t update'));
        }
        if (isset($request['name']) && $request['name']) {
            $application->name = $request['name'];
        }
        if (isset($request['icon']) && $request['icon']) {
            $application->icon = $request['icon'];
        }
        $application->allow_any_order = $request['allow_any_order'] ? true : false;
        $application->testout_attempts = $request['testout_attempts'] >= -1 ? intval($request['testout_attempts']) : 0;
        $application->question_num = $request['question_num'] ?: 3;
        $application->save();
        $application->updateTree($request);
        return redirect('/applications')->with(array('message' => 'Updated successfully'));
    }

    public function destroy($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $app = Application::where('id', $id)->first();
        DB::table('classes_applications')->where('app_id', $id)->delete();
        $app->deleteTree();
        $app->delete();
        return redirect('/applications')->with(array('message' => 'Deleted successfully'));
    }

    public function find(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        if ($request['id']) {
            $app = Application::where('id', $request['id'])->get();
            if ($app) {
                return $app;
            }
        }
        $limit = $request['limit'] == 'all' ? null : ((int)$request['limit'] > 0 ? (int)$request['limit'] : 5);
        $pattern = $request['pattern'];
        $query = Application::query()->where('name', 'LIKE', '%'.$pattern.'%');
        if ($limit) $query->limit($limit);
        return $query->get();
    }
}
