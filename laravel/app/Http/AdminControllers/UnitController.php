<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Unit;
use App\Level;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $levels = Level::all();

        $query = Unit::query();

        $query->when($request->has('level_id') && ($request->level_id >= 0), function ($q) {
            return $q->where('level_id', request('level_id'));
        });

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

        $units = $query->paginate(10)->appends(Input::except('page'));

        return view('unit_views.index', ['levels'=>$levels, 'units'=>$units, 'level_id' => $request->level_id]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lid = "";
        $levels = DB::select('select * from level');
        $units = DB::table('unit')->where('level_id', $lid)->get();
        $total_unit = Unit::all()->count();
        return view('unit_views.create', [
            'levels' => $levels,
            'units' => $units,
            'lid' => $lid,
            'total_unit' => $total_unit,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Store topic title and unit_id into topic table
        $this->validate($request, [
         'level_id'    => 'required',
         'unit_title'=> 'required',
        ]);
        DB::table('unit')->insert([
         'title' => $request['unit_title'],
         'dependency' => $request['dependency'] ?: false,
         'dev_mode' => $request['dev_mode'] ?: false,
         'level_id' => $request['level_id'],
         'order_no' => $request['order_no'],
         'created_at' => date('Y-m-d H:i:s'),
         'modified_at' => date('Y-m-d H:i:s')
        ]);
        $level_id = $request->input('level_id');
        return redirect('/unit_views?level_id='. $level_id)->with(array('message'=> 'Created successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return "Under Construction";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $unit = DB::table('unit')
            ->join('level', 'unit.level_id', '=', 'level.id')
            ->select('unit.*', 'level.title as ltitle', 'level.id as lid')
            ->where('unit.id', '=', $id)->first();
        $levels = DB::table('level')->select('id', 'title')->where('id', $unit->lid)->get();
        $total_unit = Unit::all()->count();
        return view('unit_views.edit', [
            'levels'=>$levels,
            'unit'=>$unit,
            'total_unit'=>$total_unit,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'level_id'    => 'required',
            'unit_title'=> 'required'
        ]);
        DB::table('unit')->where('id', $id)->update([
            'title' => $request['unit_title'],
            'dependency' => $request['dependency'] ?: false,
            'dev_mode' => $request['dev_mode'] ?: false,
            'level_id' => $request['level_id'],
            'order_no' => $request['order_no'],
            'created_at' => date('Y-m-d H:i:s'),
            'modified_at' => date('Y-m-d H:i:s')
        ]);
        $level_id = $request->input('level_id');
        return redirect('/unit_views?level_id='. $level_id)->with(array('message'=> 'Updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $level_id = $request->input('level_id');
        Unit::where('id', $id)->delete();
        return redirect('/unit_views?level_id='. $level_id)->with(array('message'=> 'Deleted successfully'));
    }
}
