<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Lesson;

class LessonController extends Controller
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
		$topics = DB::select('select * from topic');
		$lessons = DB::table('lesson')->where('topic_id',$request->topic_id)->get();
		return view('lesson_views.index',['levels'=>$levels,'units'=>$units,'topics'=>$topics,'lessons'=>$lessons]); 
		
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   $lid = "";
		$uid = "";
		$tid = "";
		//$lessons = "";
        $levels = DB::select('select * from level');
		$units = DB::select('select * from unit');
		$topics = DB::select('select * from topic');
		$lessons = DB::table('lesson')->where('topic_id',$tid)->get();
		$total_lesson = Lesson::all()->count();

		return view('lesson_views.create',[
			'levels' => $levels,
			'units' => $units,
			'topics' => $topics,
			'lessons' => $lessons,
			'lid' => $lid,
			'uid' => $uid,
			'tid' => $tid,
			'total_lesson' => $total_lesson
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
		$lid = $request->level_id;
		$uid = $request->unit_id;
		$tid = $request->topic_id;
		$this->validate($request, [
			'level_id'	=> 'required',
			'unit_id'	=> 'required',
			'topic_id'	=> 'required',
			'lesson_title'=> 'required',
		]);
		 
		DB::table('lesson')->insert([
			'title' => $request['lesson_title'], 
			'order_no' => $request['order_no'], 
			'dependency' => $request['dependency'] ?: false,
			'topic_id' => $request['topic_id'],
			'created_at' => date('Y-m-d H:i:s'),
			'modified_at' => date('Y-m-d H:i:s')
		]);
		
		$levels = DB::select('select * from level');
		$units = DB::select('select * from unit');
		$topics = DB::select('select * from topic');
		$lessons = DB::table('lesson')->where('topic_id',$request->topic_id)->get();
		$total_lesson = Lesson::all()->count();

		\Session::flash('flash_message','successfully saved.');
		return view('lesson_views.create',[
			'levels' => $levels,
			'units' => $units,
			'topics' => $topics,
			'lessons' => $lessons,
			'lid' => $lid,
			'uid' => $uid,
			'tid' => $tid,
			'total_lesson' => $total_lesson
		]); 
		
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
		//$lesson = DB::table('lesson')->where('id', '=', $id)->first();
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
        
		$lesson = DB::table('lesson')
			->join('topic', 'lesson.topic_id', '=', 'topic.id')
			->join('unit', 'topic.unit_id', '=', 'unit.id')
			->join('level', 'unit.level_id', '=', 'level.id')
			->select('lesson.*', 'topic.title as ttitle',
			'topic.id as tid','unit.title as utitle','unit.id as uid','level.title as ltitle','level.id as lid')
			->where('lesson.id', '=', $id)->first();

		$levels = DB::select('select * from level');
		$units = DB::table('unit')->select('id', 'title')->where('level_id', $lesson->lid)->get();
		$topics = DB::table('topic')->select('id', 'title')->where('unit_id', $lesson->uid)->get();
		$total_lesson = Lesson::all()->count();

		return view('lesson_views.edit', [
			'lesson' => $lesson,
			'levels' => $levels,
			'units' => $units,
			'topics' => $topics,
			'total_lesson' => $total_lesson
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
        $lid = $request->level_id;
		$uid = $request->unit_id;
		$tid = $request->topic_id;
		$this->validate($request, [
		 'level_id'	=> 'required',
		 'unit_id'	=> 'required',
		 'topic_id'	=> 'required',
		 'lesson_title'=> 'required',
		 ]);
		 
		 DB::table('lesson')->where('id',$id)->update([
		 'title' => $request['lesson_title'],
         'dependency' => $request['dependency'] ?: false,
		 'topic_id' => $request['topic_id'],
		 'order_no' => $request['order_no'],
		 'created_at' => date('Y-m-d H:i:s'),
		 'modified_at' => date('Y-m-d H:i:s')
		]);
		
		$levels = DB::select('select * from level');
		$units = DB::select('select * from unit');
		$topics = DB::select('select * from topic');
		$lessons = DB::table('lesson')->where('topic_id',$request->topic_id)->get();
		$total_lesson = Lesson::all()->count();

		return view('lesson_views.create',[
			'levels' => $levels,
			'units' => $units,
			'topics' => $topics,
			'lessons' => $lessons,
			'lid' => $lid,
			'uid' => $uid,
			'tid' => $tid,
			'total_lesson' => $total_lesson
		]); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
	 * The delete operation need to be performed
	 * after selecting topic_id form the lesson table 
	 * for associated lesson id.
     */
    public function destroy($id)
    {
        
		$levels = DB::select('select * from level');
		$units = DB::select('select * from unit');
		$topics = DB::select('select * from topic');
		$topic_id = DB::table('lesson')->select('topic_id')->where('id',$id)->first();
		DB::table('lesson')->where('id', $id)->delete();
		$lessons = DB::table('lesson')->where('topic_id',$topic_id->topic_id)->get();
		return view('lesson_views.index',['levels'=>$levels,'units'=>$units,'topics'=>$topics,'lessons'=>$lessons]); 

    }
}
