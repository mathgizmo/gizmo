<?php

namespace App\Http\Controllers;

use App\Student;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::select(DB::raw('students.*,(SELECT `date` FROM `students_tracking` WHERE students_tracking.user_id = students.id ORDER by id DESC LIMIT 1) as `date`'))
            ->filter(request()->all())
            ->orderBy(request()->sort ? request()->sort : 'id', request()->order ? request()->order : 'desc')
            ->get();

        return view('student_view.index', compact('students'));
    }

    public function show(Student $student)
    {
        return view('student_view.show', compact('student'));
    }
}
