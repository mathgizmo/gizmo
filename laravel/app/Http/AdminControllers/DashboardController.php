<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Dashboard;

class DashboardController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Dashboard::class); // not working!
    }

    public function index(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $query = Dashboard::query();
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
        return view('dashboards.index', [
            'dashboards' => $query->get()
        ]);
    }

    public function create()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $dashboards = Dashboard::all();
        return view('dashboards.create', array(
            'dashboards' => $dashboards,
            'total_dashboards' => $dashboards->count()
        ));
    }

    public function store(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
            'title'=> 'required',
         ]);
         DB::table('dashboards')->insert([
             'title' => $request['title'],
             'data' => $request['data'],
             'order_no' => $request['order_no'],
             'is_for_student' => $request['is_for_student'] ? true : false,
             'is_for_teacher' => $request['is_for_teacher'] ? true : false
        ]);
        return redirect('/dashboards')->with(array('message'=> 'Created successfully'));
    }

    public function show()
    {
        return "Under Construction";
    }

    public function edit($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $dashboard = Dashboard::find($id);
        $total_dashboards = Dashboard::all()->count();
        return view('dashboards.edit', [
            'dashboard' => $dashboard,
            'total_dashboards'=>$total_dashboards,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
            'title'  => 'required',
        ]);
        DB::table('dashboards')->where('id', $id)->update([
            'title' => $request['title'],
            'data' => $request['data'],
            'order_no' => $request['order_no'],
            'is_for_student' => $request['is_for_student'] ? true : false,
            'is_for_teacher' => $request['is_for_teacher'] ? true : false
        ]);
        return redirect('/dashboards')->with(array('message'=> 'Updated successfully'));
    }

    public function destroy($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        DB::table('dashboards')->where('id', $id)->delete();
        return redirect('/dashboards')->with(array('message'=> 'Deleted successfully'));
    }
}
