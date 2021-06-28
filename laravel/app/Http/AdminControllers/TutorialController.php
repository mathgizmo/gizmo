<?php

namespace App\Http\AdminControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Tutorial;

class TutorialController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Tutorial::class); // not working!
    }

    public function index(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $query = Tutorial::query();
        if ($request['id']) {
            $query->where('id', $request['id']);
        }
        if ($request['order_no']) {
            $query->where('order_no', $request['order_no']);
        }
        if ($request['title']) {
            $query->where('title', 'LIKE', '%'.$request['title'].'%');
        }
        if ($request['for']) {
            if ($request['for'] == 'student') {
                $query->where('is_for_student', true);
            }
            if ($request['for'] == 'teacher') {
                $query->where('is_for_teacher', true);
            }
        }
        if ($request['sort'] && $request['order']) {
            $query->orderBy($request['sort'], $request['order']);
        } else {
            $query->orderBy('order_no', 'ASC');
        }
        return view('tutorials.index', [
            'tutorials' => $query->get()
        ]);
    }

    public function create()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $tutorials = Tutorial::all();
        return view('tutorials.create', array(
            'tutorials' => $tutorials,
            'total_tutorials' => $tutorials->count()
        ));
    }

    public function store(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
            'title'=> 'required',
         ]);
         DB::table('tutorials')->insert([
             'title' => $request['title'],
             'data' => $request['data'],
             'order_no' => $request['order_no'],
             'is_for_student' => $request['is_for_student'] ? true : false,
             'is_for_teacher' => $request['is_for_teacher'] ? true : false
        ]);
        return redirect('/tutorials')->with(array('message'=> 'Created successfully'));
    }

    public function show()
    {
        return "Under Construction";
    }

    public function edit($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        return view('tutorials.edit', [
            'tutorial' => Tutorial::find($id),
            'total_tutorials'=>Tutorial::all()->count(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
            'title'  => 'required',
        ]);
        DB::table('tutorials')->where('id', $id)->update([
            'title' => $request['title'],
            'data' => $request['data'],
            'order_no' => $request['order_no'],
            'is_for_student' => $request['is_for_student'] ? true : false,
            'is_for_teacher' => $request['is_for_teacher'] ? true : false
        ]);
        return redirect('/tutorials')->with(array('message'=> 'Updated successfully'));
    }

    public function destroy($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        DB::table('tutorials')->where('id', $id)->delete();
        return redirect('/tutorials')->with(array('message'=> 'Deleted successfully'));
    }
}
