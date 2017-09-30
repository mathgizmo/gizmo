<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		$levels = DB::select('select * from level');
		$units = DB::table('unit')->where('level_id',$request->level_id)->get();
		//$topics = DB::table('topic')->where('unit_id',$request->unit_id)->get();
		return view('unit_views.index',['levels'=>$levels,'units'=>$units]); 
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
		$units = DB::table('unit')->where('level_id',$lid)->get();
		return view('unit_views.create',['levels'=>$levels,'units'=>$units,'lid'=>$lid]); 
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
		$lid = $request->level_id;
		//$uid = $request->unit_id;
		//$tid = $request->topic_id;
		$this->validate($request, [
		 'level_id'	=> 'required',
		 'unit_title'=> 'required',
		 'dependency'=> 'required',
		 ]);
		 
		 DB::table('unit')->insert([
		 'title' => $request['unit_title'], 
		 'dependency' => $request['dependency'],
		 'level_id' => $request['level_id'],
		 'created_at' => date('Y-m-d H:i:s'),
		 'modified_at' => date('Y-m-d H:i:s')
		]);
		
		$levels = DB::select('select * from level');
		$units = DB::table('unit')->where('level_id',$lid)->get();
		\Session::flash('flash_message','successfully saved.');
		return view('unit_views.create',['levels'=>$levels,'units'=>$units,'lid'=>$lid]); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
			->select('unit.*', 'level.title as ltitle','level.id as lid')
			->where('unit.id', '=', $id)->first();
		$levels = DB::table('level')->select('id', 'title')->where('id', $unit->lid)->get();
		//print_r($qtypes);
		return view('unit_views.edit', ['levels'=>$levels,
		'unit'=>$unit]);
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
		//
		$lid = $request->level_id;
		//$uid = $request->unit_id;
		//$tid = $request->topic_id;
		$this->validate($request, [
		 'level_id'	=> 'required',
		 //'unit_id'	=> 'required',
		 'unit_title'=> 'required',
		 'dependency'=> 'required',
		 ]);
		 
		 DB::table('unit')->where('id',$id)->update([
		 'title' => $request['unit_title'], 
		 'dependency' => $request['dependency'],
		 'level_id' => $request['level_id'],
		 'created_at' => date('Y-m-d H:i:s'),
		 'modified_at' => date('Y-m-d H:i:s')
		]);
		
		$levels = DB::select('select * from level');
		$units = DB::table('unit')->where('level_id',$request->level_id)->get();
//		\Session::flash('flash_message','successfully saved.');

		return view('unit_views.create',['levels'=>$levels,'units'=>$units,'lid'=>$lid]); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
		return "Under Construction";
    }
}
