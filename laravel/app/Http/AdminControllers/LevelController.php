<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Level;

class LevelController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Level::class); // not working!
    }

    public function index(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $query = Level::query();
        $query->when($request->has('id'), function ($q) {
            return $q->where('id', request('id'));
        });
        $query->when($request->has('order_no'), function ($q) {
            return $q->where('order_no', request('order_no'));
        });
        $query->when($request->has('title'), function ($q) {
            return $q->where('title', 'LIKE', '%'.request('title').'%');
        });
        $query->when($request->has('sort') and $request->has('order'), function ($q) {
            return $q->orderBy(request('sort'), request('order'));
        });
        $levels = $query->get();
        return view('level_views.index', ['levels'=>$levels]);
    }

    public function create()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $total_level = Level::all()->count();
        $levels = Level::All();
        return view('level_views.create', array(
            'levels' => $levels,
            'total_level' => $total_level
        ));
    }

    public function store(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
         'title'=> 'required',
         ]);
         DB::table('level')->insert([
         'title' => $request['title'],
         'dependency' => $request['dependency'] ?: false,
         'dev_mode' => $request['dev_mode'] ?: false,
         'order_no' => $request['order_no'],
         'created_at' => date('Y-m-d H:i:s'),
         'updated_at' => date('Y-m-d H:i:s')
        ]);
        return redirect('/level_views')->with(array('message'=> 'Created successfully'));
    }

    public function show()
    {
        return "Under Construction";
    }

    public function edit($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $level = Level::find($id);
        $total_level = Level::all()->count();
        return view('level_views.edit', [
            'level'=>$level,
            'total_level'=>$total_level,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
            'title'  => 'required',
        ]);
        DB::table('level')->where('id', $id)->update([
            'title' => $request['title'],
            'order_no' => $request['order_no'],
            'dependency' => $request['dependency'] ?: false,
            'dev_mode' => $request['dev_mode'] ?: false,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return redirect('/level_views')->with(array('message'=> 'Updated successfully'));
    }

    public function destroy($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        Level::where('id', $id)->delete();
        return redirect('/level_views')->with(array('message'=> 'Deleted successfully'));
    }
}
