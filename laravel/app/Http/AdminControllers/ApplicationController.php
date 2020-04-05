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
        $query->when($request->has('id'), function ($q) {
            return $q->where('id', request('id'));
        });
        $query->when($request->has('name'), function ($q) {
            return $q->where('name', 'LIKE', '%' . request('name') . '%');
        });
        $query->when($request->has('sort') and $request->has('order'), function ($q) {
            return $q->orderBy(request('sort'), request('order'));
        });
        $applications = $query->get();
        return view('application_views.index', ['applications' => $applications]);
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
        return view('application_views.create', array(
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
        $application->save();
        $application->updateTree($request);
        return redirect('/application_views')->with(array('message' => 'Created successfully'));
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
        return view('application_views.edit', [
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
            return redirect('/application_views')->with(array('message' => 'Can\'t update'));
        }
        if (isset($request['name']) && $request['name']) {
            $application->name = $request['name'];
        }
        if (isset($request['icon']) && $request['icon']) {
            $application->icon = $request['icon'];
        }
        $application->save();
        $application->updateTree($request);
        return redirect('/application_views')->with(array('message' => 'Updated successfully'));
    }

    public function destroy($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $app = Application::where('id', $id)->first();
        $app->deleteTree();
        $app->delete();
        return redirect('/application_views')->with(array('message' => 'Deleted successfully'));
    }
}
