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
        $query = Student::select(DB::raw('students.*,(SELECT `date` FROM `students_tracking`
            WHERE students_tracking.student_id = students.id ORDER by id DESC LIMIT 1) as `date`'))
            ->where('email', 'NOT LIKE', '%@somemail.com')
            ->filter(request()->all())
            ->orderBy(request()->sort ? request()->sort : 'id', request()->order ? request()->order : 'desc');

        $students = $query->paginate(10)->appends(request()->query());
        return view('students.index', compact('students'));
    }

    public function edit(Student $student)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        return view('students.edit', compact('student'));
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
}
