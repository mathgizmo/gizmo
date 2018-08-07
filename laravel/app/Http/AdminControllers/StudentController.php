<?php

namespace App\Http\Controllers;

use App\Student;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::select(DB::raw('students.*,(SELECT `date` FROM `students_tracking`
            WHERE students_tracking.student_id = students.id ORDER by id DESC LIMIT 1) as `date`'))
            ->where('email', 'NOT LIKE', '%@somemail.com')
            ->filter(request()->all())
            ->orderBy(request()->sort ? request()->sort : 'id', request()->order ? request()->order : 'desc')
            ->get();
        return view('student_view.index', compact('students'));
    }

    /**
     * @param Student $student
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Student $student)
    {
        return view('student_view.show', compact('student'));
    }

    /**
     * @param Student $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function superUpdate(Student $student)
    {
        $is_super = true;
        if ($student->is_super) {
            $is_super = false;
        }
        $student->update([
            'is_super' => $is_super,
        ]);
        return back();
    }

    /**
     * @param Student $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetProgress(Student $student)
    {
        DB::table('progresses')->where('student_id', $student->id)->delete();
        return back();
    }

    /**
     * @param Student $student
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete(Student $student)
    {
        $student->delete();
        return back();
    }
}
