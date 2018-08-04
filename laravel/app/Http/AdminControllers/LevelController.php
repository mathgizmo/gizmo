<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Level;

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
        $levels = Level::All();
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
        $total_level = Level::all()->count();
        $levels = Level::All();
        return view('level_views.create',array(
            'levels' => $levels,
            'total_level' => $total_level
        ));
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
         'title'=> 'required',
         ]);

         DB::table('level')->insert([
         'title' => $request['title'],
         'dependency' => $request['dependency'] ?: false,
         'order_no' => $request['order_no'],
         'created_at' => date('Y-m-d H:i:s'),
         'updated_at' => date('Y-m-d H:i:s')
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
        $level = Level::find($id);
        $total_level = Level::all()->count();

        return view('level_views.edit', [
            'level'=>$level,
            'total_level'=>$total_level,
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
            'title'  => 'required',
        ]);

        DB::table('level')->where('id',$id)->update([
            'title' => $request['title'],
            'order_no' => $request['order_no'],
            'dependency' => $request['dependency'] ?: false,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect('/level_views')->with( array('message'=> 'Updated successfully') );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Level::where('id', $id)->delete();
        return redirect('/level_views')->with( array('message'=> 'Deleted successfully') );
    }
}
