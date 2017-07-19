<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Http\Requests;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('question_views.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {	$levels = DB::select('select * from level');
	   
	    if($request->ajax()){
		$output= '';
		$id = $request->level_id;
		$units = DB::table('unit')->select('id', 'title')->where('level_id', $id)->get();
       if($units){

			foreach($units as $unit){
				$output.='<option values="'.$unit->id.'">'.$unit->title.'</option>';
				}
			return response()->json($output);
			
			}
		//return response('<option values="aa"> AAA</option>', 200);
		//return response()->json($units);

	}; // request ajax if close
		
		$units = DB::select('select * from unit');
		$topics = DB::select('select * from topic');
		$lessons = DB::select('select * from lesson');
		$qtypes = DB::select('select * from question_type');
		$qrmodes = DB::select('select * from reply_mode');
        return view('question_views.create',['lessons' => $lessons, 
		'qtypes' => $qtypes, 'qrmodes' => $qrmodes, 'levels' => $levels, 
		'units' => $units, 'topics' => $topics]);
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
		DB::table('question')->insert([
		'lesson_id' => $request['lesson_id'],
		'mandatoriness' => $request['mandatoriness'],
		'type' => $request['type'],
		'reply_mode' => $request['reply_mode'],
		'question' => $request['question'],
		'image' => $request['image'],
		'shape' => $request['shape'],
		'min_value' => $request['min_value'],
		'max_value' => $request['max_value'],
		'initial_position' => $request['initial_position'],
		'step_value' => $request['step_value'],
		'mcq1' => $request['mcq1'],
		'mcq2' => $request['mcq2'],
		'mcq3' => $request['mcq3'],
		'mcq4' => $request['mcq4'],
		'mcq5' => $request['mcq5'],
		'mcq6' => $request['mcq6'],
		'answer' => $request['answer'],
		'explanation' => $request['explanation'],
		'feedback' => $request['feedback'],
		//'created_at' => $request['passkey'],
		//'modified_at' => $request['passkey'],
		
		
		]);
		return "Inserted";
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
        //
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
    }
}
