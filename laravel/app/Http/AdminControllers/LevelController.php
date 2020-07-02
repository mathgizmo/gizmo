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
        if ($request['id']) {
            $query->where('id', $request['id']);
        }
        if ($request['order_no']) {
            $query->where('order_no', $request['order_no']);
        }
        if ($request['title']) {
            $query->where('title', 'LIKE', '%'.$request['title'].'%');
        }
        if ($request['sort'] && $request['order']) {
            $query->orderBy($request['sort'], $request['order']);
        }
        return view('levels.index', [
            'levels' => $query->get()
        ]);
    }

    public function create()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $total_level = Level::all()->count();
        $levels = Level::All();
        return view('levels.create', array(
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
        return redirect('/levels')->with(array('message'=> 'Created successfully'));
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
        return view('levels.edit', [
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
        return redirect('/levels')->with(array('message'=> 'Updated successfully'));
    }

    public function destroy($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        Level::where('id', $id)->delete();
        return redirect('/levels')->with(array('message'=> 'Deleted successfully'));
    }
}
