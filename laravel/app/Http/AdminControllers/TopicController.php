<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		$levels = DB::select('select * from level');
		$units = DB::select('select * from unit');
		$topics = DB::table('topic')->where('unit_id',$request->unit_id)->get();
		return view('topic_views.index',['levels'=>$levels,'units'=>$units,'topics'=>$topics]); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lid = "";
		$uid = "";
		$levels = DB::select('select * from level');
		$units = DB::select('select * from unit');
		$topics = DB::table('topic')->where('unit_id',$uid)->get();
		return view('topic_views.create',['levels'=>$levels,'units'=>$units,'topics'=>$topics,'lid'=>$lid,'uid'=>$uid]); 
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
		$uid = $request->unit_id;
		//$tid = $request->topic_id;
		$this->validate($request, [
		 'level_id'	=> 'required',
		 'unit_id'	=> 'required',
		 'topic_title'=> 'required',
		 'dependency'=> 'required',
		 ]);
		 
		 DB::table('topic')->insert([
		 'title' => $request['topic_title'], 
		 'dependency' => $request['dependency'],
		 'unit_id' => $request['unit_id'],
		 'created_at' => date('Y-m-d H:i:s'),
		 'modified_at' => date('Y-m-d H:i:s')
		]);
		
		$levels = DB::select('select * from level');
		$units = DB::select('select * from unit');
		$topics = DB::table('topic')->where('unit_id',$uid)->get();
		//$lessons = DB::table('lesson')->where('topic_id',$request->topic_id)->get();
		\Session::flash('flash_message','successfully saved.');
		return view('topic_views.create',['levels'=>$levels,'units'=>$units,'topics'=>$topics,'lid'=>$lid,'uid'=>$uid]); 
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $topic = DB::table('topic')
			//->join('topic', 'lesson.topic_id', '=', 'topic.id')
			->join('unit', 'topic.unit_id', '=', 'unit.id')
			->join('level', 'unit.level_id', '=', 'level.id')
			->select('topic.*', 'unit.title as utitle','unit.id as uid','level.title as ltitle','level.id as lid')
			->where('topic.id', '=', $id)->first();
		$levels = DB::select('select * from level');
		$units = DB::table('unit')->select('id', 'title')->where('level_id', $topic->lid)->get();
		//$topics = DB::table('topic')->select('id', 'title')->where('unit_id', $lesson->uid)->get();
		//print_r($qtypes);
		return view('topic_views.edit', ['levels'=>$levels,
		'units'=>$units,'topic'=>$topic]);
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
		$uid = $request->unit_id;
		//$tid = $request->topic_id;
		$this->validate($request, [
		 'level_id'	=> 'required',
		 'unit_id'	=> 'required',
		 'topic_title'=> 'required',
		 'dependency'=> 'required',
		 ]);
		 
		 DB::table('topic')->where('id',$id)->update([
		 'title' => $request['topic_title'], 
		 'dependency' => $request['dependency'],
		 'unit_id' => $request['unit_id'],
		 'created_at' => date('Y-m-d H:i:s'),
		 'modified_at' => date('Y-m-d H:i:s')
		]);
		
		$levels = DB::select('select * from level');
		$units = DB::select('select * from unit');
		$topics = DB::table('topic')->where('unit_id',$request->unit_id)->get();
		//$lessons = DB::table('lesson')->where('topic_id',$request->topic_id)->get();
//		\Session::flash('flash_message','successfully saved.');

		return view('topic_views.create',['levels'=>$levels,'units'=>$units,'topics'=>$topics,'lid'=>$lid,'uid'=>$uid]); 
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