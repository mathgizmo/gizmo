<?php

namespace App\Http\AdminControllers;

use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Tymon\JWTAuth\Facades\JWTAuth;

class StudentController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Student::class);
    }

    public function index()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $allowedPagination = [ 10,25,50,100,250,500,1000 ]; // define allowed values for per_page
        $perPage = (int) request('per_page',10); // fetch the per_page value from url query, default to 10
        $perPage = in_array($perPage, $allowedPagination) ? $perPage : 10; // checks if value is valid
        $query = Student::select(DB::raw('students.*,(SELECT `date` FROM `students_tracking`
            WHERE students_tracking.student_id = students.id ORDER by id DESC LIMIT 1) as `date`'))
            ->where('email', 'NOT LIKE', '%@somemail.com')
            ->filter(request()->all());
        // Tag filter logic
        $tagIds = collect(explode(',', request('tag_id', '')))->filter(fn($id) => is_numeric($id))->unique()->toArray();
        if (in_array('none', explode(',', request('tag_id', '')))) {
            $query->whereDoesntHave('tags');
        } elseif (!empty($tagIds)) {
            $query->whereHas('tags', function($q) use ($tagIds) {
                $q->whereIn('tag.id', $tagIds);
            });
        }
        $query->orderBy(request()->sort ? request()->sort : 'id', request()->order ? request()->order : 'desc');
        $tags = \App\Tag::orderBy('order_no')->get();
        $selected_tag_ids = request()->has('tag_id') ? (array) request()->input('tag_id') : [];
        $students = $query->paginate($perPage)->appends(request()->query());
        return view('students.index', compact('students', 'tags', 'selected_tag_ids'));
    }

    public function edit(Student $student)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $tags = \App\Tag::orderBy('order_no')->get();
        $selected_tag_ids = $student->tags()->pluck('tag_id')->toArray();
        return view('students.edit', compact('student', 'tags', 'selected_tag_ids'));
    }

    public function update(Request $request, Student $student)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
            'tag_id' => 'nullable|array',
            'tag_id.*' => 'exists:tag,id'
        ]);

        // Sync the tags (handles both attaching and detaching)
        if ($request->has('tag_id')) {
            $tagIds = array_filter((array)$request->input('tag_id'), function($id) {
                return !empty($id) && $id !== 'none';
            });
            $student->tags()->sync($tagIds);
        } else {
            $student->tags()->detach(); // Remove all tags if none selected
        }

        return back()->with('message', 'Updated successfully');
    }

    public function superUpdate(Request $request, Student $student)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $student->update([
            'is_super' => $request['is_super'] ? true : false,
        ]);
        return back();
    }

    public function selfStudyUpdate(Request $request, Student $student)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $student->update([
            'is_self_study' => $request['is_self_study'] ? true : false,
        ]);
        return back();
    }

    public function teacherUpdate(Request $request, Student $student)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $student->update([
            'is_teacher' => $request['is_teacher'] ? true : false,
        ]);
        return back();
    }

    public function researcherUpdate(Request $request, Student $student)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $student->update([
            'is_researcher' => $request['is_researcher'] ? true : false,
        ]);
        return back();
    }

    public function resetProgress(Student $student)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        DB::table('progresses')->where('student_id', $student->id)->delete();
        return back();
    }

    public function delete(Student $student)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $student->delete();
        return back();
    }

    public function find(Request $request) {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $student = null;
        $query = Student::query();
        if ($request['is_teacher']) {
            $query->where('is_teacher', true);
        }
        if ($request['id']) {
            $student = $query->where('id', $request['id'])->get();
        }
        if ($student) {
            return $student;
        } else {
            $limit = $request['limit'] == 'all' ? null : ((int) $request['limit'] > 0 ? (int) $request['limit'] : 5);
            $pattern = $request['pattern'];
            $query->where(function ($q) use($pattern) {
                $q->where('email', 'LIKE', $pattern.'%');
                $q->orWhere('first_name', 'LIKE', $pattern.'%');
                $q->orWhere('last_name', 'LIKE', $pattern.'%');
            });
            $names = explode(' ', $pattern);
            if (count($names) > 1) {
                $query->orWhere(function ($q) use($names) {
                    $q->where('first_name', 'LIKE', $names[0].'%')
                        ->where('last_name', 'LIKE', $names[1].'%');
                });
            }
            if ($limit) $query->limit($limit);
            return $query->get();
        }
    }

    public function loginAsStudent(Student $student) {
        return Redirect::to(URL::to(Config::get('app.login_as_student_url'))
            .'?token='.JWTAuth::fromUser($student));
    }

    public function updateTags(Request $request, Student $student)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $tagIds = collect($request->input('tag_id', []))
            ->filter(fn($id) => !empty($id) && is_numeric($id))
            ->unique()->toArray();
        $student->tags()->sync($tagIds);
        return back()->with('message', 'Tags updated successfully.');
    }
}
