<?php

namespace App\Http\AdminControllers;

use Illuminate\Http\Request;
use App\Application;
use App\ClassOfStudents;
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
        if ($request['type']) {
            $query->where('type', $request['type']);
        }
        if ($request['tag_id']) {
            if ($request['tag_id'] === 'none') {
                $query->whereDoesntHave('tags');
            } else {
                $tagId = $request['tag_id'];
                $query->whereHas('tags', function($qq) use ($tagId) {
                    $qq->where('tag.id', $tagId);
                });
            }
        }
        if ($request['teacher']) {
            $teacher = $request['teacher'];
            $query->whereHas('teacher', function ($q) use ($teacher) {
                $q->where('email', 'LIKE', '%'.$teacher.'%');
            });
        }
        if ($request['sort'] && $request['order']) {
            if ($request['sort'] == 'teacher') {
                $query->leftJoin('students', 'students.id', '=', 'applications.teacher_id')
                    ->orderBy('students.email', request('order'))->select('applications.*');
            } else {
                $query->orderBy($request['sort'], $request['order']);
            }
        }
        return view('applications.index', ['applications' => $query->paginate(10)->appends(request()->query())]);
    }

    public function create(Request $request)
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
            'tree' => $tree,
            'type' => $request->input('type'),
            'selected_tag_ids' => []
        ));
    }

    public function store(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
    $this->validate($request, [
            'name' => 'required',
            'tag_id' => 'nullable|array',
            'tag_id.*' => 'exists:tag,id'
        ]);
        $app = new Application();
        $app->name = $request['name'];
    $tagIds = collect((array)($request['tag_id'] ?? []))->filter()->unique()->values()->all();
        if (isset($request['icon']) && $request['icon']) {
            $app->icon = $request['icon'];
        }
        $app->allow_any_order = $request['allow_any_order'] ? true : false;
        $app->allow_back_tracking = $request['allow_back_tracking'] ? true : false;
        $app->testout_attempts = $request['testout_attempts'] >= -1 ? intval($request['testout_attempts']) : 0;
        $app->question_num = $request['question_num'] ?: 3;
        $app->type = $request['type'] ?: 'assignment';
        $app->duration = $request['duration'] ?: null;
        $app->save();
        if (!empty($tagIds)) {
            $app->tags()->sync($tagIds);
        } else {
            $app->tags()->sync([]);
        }
        $app->updateTree($request);
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
    $selected_tag_ids = $application->tags->pluck('id')->toArray();
        return view('applications.edit', [
            'application' => $application,
            'icons' => $icons,
            'tree' => $tree,
            'selected_tag_ids' => $selected_tag_ids
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
            'name' => 'required',
            'tag_id' => 'nullable|array',
            'tag_id.*' => 'exists:tag,id'
        ]);
        $app = Application::where('id', $id)->first();
        if (!$app) {
            return redirect('/applications')->with(array('message' => 'Can\'t update'));
        }
        if (isset($request['name']) && $request['name']) {
            $app->name = $request['name'];
        }
        if (isset($request['icon']) && $request['icon']) {
            $app->icon = $request['icon'];
        }
    $tagIds = collect((array)($request['tag_id'] ?? []))->filter()->unique()->values()->all();
        $app->allow_any_order = $request['allow_any_order'] ? true : false;
        $app->allow_back_tracking = $request['allow_back_tracking'] ? true : false;
        $app->testout_attempts = $request['testout_attempts'] >= -1 ? intval($request['testout_attempts']) : 0;
        $app->question_num = $request['question_num'] ?: 3;
        $app->duration = $request['duration'] ?: null;
    $app->save();
    $app->tags()->sync($tagIds);
        $app->updateTree($request);
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

    public function copy(Request $request, Application $application) {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $copy = $application->replicateWithRelations();
        return redirect()->route('applications.edit', $copy)->with(array('message' => 'Copied successfully!'));
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
        $pattern = trim((string)$request->input('pattern', ''));
        $query = Application::query()->with(['tags:id,name']);
        if ($pattern !== '') {
            $terms = array_values(array_filter(array_map('trim', preg_split('/\s+/', $pattern))));
            $query->where(function($q) use ($pattern, $terms) {
                // match full phrase
                $q->where('name', 'LIKE', '%'.$pattern.'%');
                // or match all terms
                if (!empty($terms)) {
                    $q->orWhere(function($qq) use ($terms) {
                        foreach ($terms as $term) {
                            $qq->where('name', 'LIKE', '%'.$term.'%');
                        }
                    });
                }
            });
        }
        if ($request['type']) {
            $query->where('type', $request['type']);
        }
        // If tag_id[] is provided directly (from UI), filter by those; otherwise, if class_id is provided, filter by class tags
        $filterTagIds = collect((array)$request->input('tag_id', []))->filter()->map(function($v){ return (int)$v; })->values()->all();
        if (!empty($filterTagIds)) {
            $query->whereHas('tags', function($q) use ($filterTagIds) {
                $q->whereIn('tag.id', $filterTagIds);
            });
        } elseif ($request->filled('class_id')) {
            $class = ClassOfStudents::with('tags:id')->find($request->input('class_id'));
            if ($class && $class->tags && $class->tags->count() > 0) {
                $tagIds = $class->tags->pluck('id')->toArray();
                $query->whereHas('tags', function($q) use ($tagIds) {
                    $q->whereIn('tag.id', $tagIds);
                });
            }
        }
        if ($limit) $query->limit($limit);
        return $query->get();
    }
}
