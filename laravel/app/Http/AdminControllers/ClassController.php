<?php

namespace App\Http\Controllers;

use App\ClassOfStudents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{

    public function index(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $query = ClassOfStudents::query();
        if ($request->has('id')) {
            $query->where('id', request('id'));
        }
        if ($request->has('name')) {
            $query->where('name', 'LIKE', '%'.request('name').'%');
        }
        if ($request->has('teacher')) {
            $teacher = $request['teacher'];
            $query->whereHas('teacher', function ($q) use ($teacher) {
                $q->where('name', 'LIKE', '%'.$teacher.'%');
            });
        }
        if ($request->has('subscription_type')) {
            $query->where('subscription_type', request('subscription_type'));
        }
        if ($request->has('sort') and $request->has('order')) {
            if (request('sort') == 'teacher') {
                $query->leftJoin('students', 'students.id', '=', 'classes.teacher_id')
                    ->orderBy('students.name', request('order'))->select('classes.*');
            } else {
                $query->orderBy(request('sort'), request('order'));
            }
        }
        return view('classes.index', ['classes' => $query->paginate(10)]);
    }

    public function create()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        return view('classes.create');
    }

    public function store(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
            'name'=> 'required',
            'teacher_id' => [
                'required', 'exists:students,id'
            ],
        ]);
        $class = ClassOfStudents::create($request->only('name', 'teacher_id', 'subscription_type', 'invitations'));
        foreach ($request['application'] as $key => $value) {
            DB::table('classes_applications')->insert([
                'class_id' => $class->id,
                'app_id' => $key,
                'due_date' => $value['due_date']
            ]);
        }
        return redirect()->route('classes.index')->with(array('message'=> 'Created successfully'));
    }

    public function edit($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $class = ClassOfStudents::where('id', $id)->first();
        if (!$class) {
            abort('404', 'Class Not Exists!');
        }
        return view('classes.edit', [
            'class' => $class,
            'applications' => $class->applications()->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
            'name'=> 'required',
            'teacher_id' => [
                'required', 'exists:students,id'
            ],
        ]);
        $class = ClassOfStudents::where('id', $id)->first();
        if (!$class) {
            abort('404', 'Class Not Exists!');
        }
        $class->update($request->only('name', 'teacher_id', 'subscription_type', 'invitations'));
        $old_apps = $class->applications()->get()->keyBy('id');
        if ($request['application']) {
            foreach ($request['application'] as $key => $value) {
                if ($old_apps->where('id', $key)->count() > 0) {
                    $old_apps->forget($key);
                    DB::table('classes_applications')->where('class_id', $class->id)->where('app_id', $key)
                        ->update(['due_date' => $value['due_date']]);
                } else {
                    DB::table('classes_applications')->insert([
                        'class_id' => $class->id,
                        'app_id' => $key,
                        'due_date' => $value['due_date']
                    ]);
                }
            }
        }
        DB::table('classes_applications')
            ->where('class_id', $class->id)
            ->whereIn('app_id', $old_apps->pluck('id')->toArray())
            ->delete();
        return redirect()->route('classes.index')->with(array('message'=> 'Updated successfully'));
    }

    public function destroy($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        DB::table('classes_applications')->where('class_id', $id)->delete();
        DB::table('classes_students')->where('class_id', $id)->delete();
        ClassOfStudents::where('id', $id)->delete();
        return redirect()->route('classes.index')->with(array('message'=> 'Deleted successfully'));
    }

    public function getStudents($class_id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $class = ClassOfStudents::where('id', $class_id)->first();
        if (!$class) {
            abort('404', 'Class Not Exists!');
        }
        return view('classes.students.index', [
            'class' => $class,
            'students' => $class->students()->orderBy('last_name', 'ASC')->orderBy('name', 'ASC')->get()
        ]);
    }

    public function find(Request $request) {
        $class = null;
        $query = ClassOfStudents::query();
        if ($request['id']) {
            $class = $query->where('id', $request['id'])->get();
        }
        if ($class) {
            return $class;
        } else {
            $limit = $request['limit'] == 'all' ? null : ((int) $request['limit'] > 0 ? (int) $request['limit'] : 5);
            $pattern = $request['pattern'];
            $query->where('name', 'LIKE', $pattern.'%');
            if ($limit) $query->limit($limit);
            return $query->get();
        }
    }

}
