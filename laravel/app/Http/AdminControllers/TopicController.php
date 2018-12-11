<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Topic;
use App\Level;
use App\Unit;

class TopicController extends Controller
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
        $units = Unit::all();

        $query = Topic::query();

        if ($request->has('unit_id') && $request->unit_id >= 0) {
            $query->where('unit_id', $request->unit_id);
        } else if ($request->has('level_id') && $request->level_id >= 0) {
            $query->whereIn('unit_id', function($query) { 
                $query->select('id')->from(with(new Unit)->getTable())
                ->where('level_id', request('level_id'));
            });
        }

        $query->when($request->has('id'), function ($q) {
            return $q->where('id', request('id'));
        });
        $query->when($request->has('order_no'), function ($q) {
            return $q->where('order_no', request('order_no'));
        });
        $query->when($request->has('title'), function ($q) {
            return $q->where('title', 'LIKE', '%'.request('title').'%');
        });
        $query->when($request->has('short_name'), function ($q) {
            return $q->where('short_name', 'LIKE', '%'.request('short_name').'%');
        });
        $query->when($request->has('sort') and $request->has('order'), function ($q) {
            return $q->orderBy(request('sort'), request('order'));
        });
        $topics = $query->get();

        foreach ($topics as $key => $value) {
            if(!file_exists($topics[$key]->icon_src)) {
                $topics[$key]->icon_src = 'images/default-icon.svg';
            }
        }
        return view('topic_views.index', ['levels'=>$levels, 'units'=>$units, 'topics'=>$topics, 'unit_id'=>$request->unit_id, 'level_id'=>$request->level_id]);
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
        $topics = DB::table('topic')->where('unit_id', $uid)->get();
        $total_topic = Topic::all()->count();
        $icons = array();
        $all = glob("images/icons/*.svg");
        $complete = glob("images/icons/*-gold.svg");
        foreach (array_diff($all, $complete) as $file) {
          $icons[] = $file;
        }
        return view('topic_views.create', array(
            'levels' => $levels,
            'units' => $units,
            'topics' => $topics,
            'lid' => $lid,
            'uid' => $uid,
			'total_topic' => $total_topic,
            'icons' => $icons
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
        // Store topic title and unit_id into topic table
        $lid = $request->level_id;
        $uid = $request->unit_id;
        //$tid = $request->topic_id;
        $this->validate($request, [
            'level_id'    => 'required',
            'unit_id'    => 'required',
            'topic_title'=> 'required'
        ]);
        DB::table('topic')->insert([
            'icon_src' => $request['icon_src'] ?: 'images/default-icon.svg',
            'short_name' => $request['short_name'],
            'dependency' => $request['dependency'] ?: false,
            'dev_mode' => $request['dev_mode'] ?: false,
            'order_no' => $request['order_no'],
            'title' => $request['topic_title'],
            'unit_id' => $request['unit_id'],
            'created_at' => date('Y-m-d H:i:s'),
            'modified_at' => date('Y-m-d H:i:s')
        ]);
        $icons = array();
        $all = glob("images/icons/*.svg");
        $complete = glob("images/icons/*-gold.svg");
        foreach (array_diff($all, $complete) as $file) {
          $icons[] = $file;
        }
        $levels = DB::select('select * from level');
        $units = DB::select('select * from unit');
        $topics = DB::table('topic')->where('unit_id', $uid)->get();
        foreach ($topics as $key => $value) {
            if(!file_exists($topics[$key]->icon_src)) {
                $topics[$key]->icon_src = 'images/default-icon.svg';
            }
        }
        $total_topic = Topic::all()->count();
        \Session::flash('flash_message', 'successfully saved.');
        return view('topic_views.create',['levels'=>$levels,'units'=>$units,'topics'=>$topics,'lid'=>$lid,'uid'=>$uid,'total_topic'=>$total_topic,'icons' => $icons]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
        $topic = DB::table('topic')
            ->join('unit', 'topic.unit_id', '=', 'unit.id')
            ->join('level', 'unit.level_id', '=', 'level.id')
            ->select('topic.*', 'unit.title as utitle', 'unit.id as uid', 'level.title as ltitle', 'level.id as lid')
            ->where('topic.id', '=', $id)->first();
        $levels = DB::select('select * from level');
        $units = DB::table('unit')->select('id', 'title')->where('level_id', $topic->lid)->get();
        $total_topic = Topic::all()->count();
        $icons = array();
        $all = glob("images/icons/*.svg");
        $complete = glob("images/icons/*-gold.svg");
        foreach (array_diff($all, $complete) as $file) {
          $icons[] = $file;
        }
        if(!file_exists($topic->icon_src)) {
            $topic->icon_src = 'images/default-icon.svg';
        }
        return view('topic_views.edit', [
            'levels'=>$levels,
            'units'=>$units,
            'topic'=>$topic,
            'total_topic' => $total_topic,
            'icons' => $icons
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
        //
        $lid = $request->level_id;
        $uid = $request->unit_id;
        //$tid = $request->topic_id;
        $this->validate($request, [
            'level_id'    => 'required',
            'unit_id'    => 'required',
            'topic_title'=> 'required'
        ]);
        $update_array = [
            'short_name' => $request['short_name'],
            'order_no' => $request['order_no'],
            'title' => $request['topic_title'],
            'dependency' => $request['dependency'] ?: false,
            'dev_mode' => $request['dev_mode'] ?: false,
            'unit_id' => $request['unit_id'],
            'created_at' => date('Y-m-d H:i:s'),
            'modified_at' => date('Y-m-d H:i:s')
        ];
        if (isset($request['icon_src']) && $request['icon_src']) {
            $update_array['icon_src'] = $request['icon_src'];
        }
        DB::table('topic')->where('id', $id)->update($update_array);
        $levels = DB::select('select * from level');
        $units = DB::select('select * from unit');
        $topics = DB::table('topic')->where('unit_id', $request->unit_id)->get();
        $total_topic = Topic::all()->count();
        $icons = array();
        $all = glob("images/icons/*.svg");
        $complete = glob("images/icons/*-gold.svg");
        foreach (array_diff($all, $complete) as $file) {
          $icons[] = $file;
        }
        foreach ($topics as $key => $value) {
            if(!file_exists($topics[$key]->icon_src)) {
                $topics[$key]->icon_src = 'images/default-icon.svg';
            }
        }
        return view('topic_views.create', [
            'levels' => $levels,
            'units' => $units,
            'topics' => $topics,
            'lid' => $lid,
            'uid' => $uid,
            'total_topic' => $total_topic,
            'icons' => $icons
        ]);
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
        $unit_id = $request->input('unit_id');
        Topic::where('id', $id)->delete();
        return redirect('/topic_views?level_id='. $level_id . '&unit_id='. $unit_id)
            ->with(array('message'=> 'Deleted successfully'));
    }

}
