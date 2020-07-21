<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Topic;
use App\Level;
use App\Unit;

class TopicController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Topic::class); // not working!
    }

    public function index(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $query = Topic::query();
        if ($request['unit_id'] && $request['unit_id'] >= 0) {
            $query->where('unit_id', $request['unit_id']);
        } else if ($request['level_id'] && $request['level_id'] >= 0) {
            $query->whereIn('unit_id', function($query) {
                $query->select('id')->from(with(new Unit)->getTable())
                ->where('level_id', request('level_id'));
            });
        }
        if ($request['id']) {
            $query->where('id', $request['id']);
        }
        if ($request['order_no']) {
            $query->where('order_no', $request['order_no']);
        }
        if ($request['title']) {
            $query->where('title', 'LIKE', '%'.$request['title'].'%');
        }
        if ($request['short_name']) {
            $query->where('short_name', 'LIKE', '%'.$request['short_name'].'%');
        }
        if ($request['sort'] && $request['order']) {
            $query->orderBy($request['sort'], $request['order']);
        }
        $topics = $query->paginate(10)->appends(request()->query());;
        foreach ($topics as $key => $value) {
            if(!file_exists($topics[$key]->icon_src)) {
                $topics[$key]->icon_src = 'images/default-icon.svg';
            }
        }
        return view('topics.index', [
            'levels' => Level::all(),
            'units' => Unit::all(),
            'topics' => $topics,
            'unit_id' => $request['unit_id'],
            'level_id' => $request['level_id']
        ]);
    }

    public function create()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
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
        return view('topics.create', array(
            'levels' => $levels,
            'units' => $units,
            'topics' => $topics,
            'lid' => $lid,
            'uid' => $uid,
			'total_topic' => $total_topic,
            'icons' => $icons
        ));
    }

    public function store(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
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
        $level_id = $request->input('level_id');
        $unit_id = $request->input('unit_id');
        return redirect('/topics?level_id='. $level_id . '&unit_id='. $unit_id)
            ->with(array('message'=> 'Created successfully'));
    }

    public function show($id)
    {
        return "Under Construction";
    }

    public function edit($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
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
        if (!file_exists($topic->icon_src)) {
            $topic->icon_src = 'images/default-icon.svg';
        }
        return view('topics.edit', [
            'levels'=>$levels,
            'units'=>$units,
            'topic'=>$topic,
            'total_topic' => $total_topic,
            'icons' => $icons
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
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
        $level_id = $request->input('level_id');
        $unit_id = $request->input('unit_id');
        return redirect('/topics?level_id='. $level_id . '&unit_id='. $unit_id)
            ->with(array('message'=> 'Updated successfully'));
    }

    public function destroy(Request $request, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        Topic::where('id', $id)->delete();
        $level_id = $request->input('level_id');
        $unit_id = $request->input('unit_id');
        return redirect('/topics?level_id='. $level_id . '&unit_id='. $unit_id)
            ->with(array('message'=> 'Deleted successfully'));
    }

}
