<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


use App\Http\Requests;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		$levels = DB::select('select * from level');
		//$units = DB::table('unit')->where('level_id',$request->level_id)->get();
		//$topics = DB::table('topic')->where('unit_id',$request->unit_id)->get();
		return view('level_views.index',['levels'=>$levels]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

		//$uid = $request->unit_id;
		//$tid = $request->topic_id;
		$this->validate($request, [
		 'level_title'=> 'required',
		 ]);
		 
		 DB::table('level')->insert([
		 'title' => $request['level_title'], 
		 'created_at' => date('Y-m-d H:i:s'),
		 'modified_at' => date('Y-m-d H:i:s')
		]);
		
		$levels = DB::select('select * from level');
		\Session::flash('flash_message','successfully saved.');
		return view('level_views.index',['levels'=>$levels]); 
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
        //
		return "Under Construction";
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
		return "Under Construction";
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