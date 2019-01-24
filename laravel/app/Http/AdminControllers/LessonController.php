<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Lesson;
use App\Topic;
use App\Level;
use App\Unit;

class LessonController extends Controller
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
        $topics = Topic::all();

        $query = Lesson::query();
        
        if ($request->has('topic_id') && $request->topic_id >= 0) {
            $query->where('topic_id', $request->topic_id);
        } else if ($request->has('unit_id') && $request->unit_id >= 0) {
            $query->whereIn('topic_id', function($query) { 
                $query->select('id')->from(with(new Topic)->getTable())
                ->where('unit_id', request('unit_id'));
            });
        } else if ($request->has('level_id')  && $request->level_id >= 0) {
            $query->whereIn('topic_id', function($query) { 
                $query->select('id')->from(with(new Topic)->getTable())
                ->whereIn('unit_id', function($query) { 
                    $query->select('id')->from(with(new Unit)->getTable())
                    ->where('level_id', request('level_id'));
                });
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
        $query->when($request->has('sort') and $request->has('order'), function ($q) {
            return $q->orderBy(request('sort'), request('order'));
        });
        
        $lessons = $query->paginate(10)->appends(Input::except('page'));

        return view('lesson_views.index', ['levels'=>$levels, 'units'=>$units, 'topics'=>$topics, 'lessons'=>$lessons]);
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
        $levels = DB::select('select * from level');
        $units = DB::select('select * from unit');
        $topics = DB::select('select * from topic');
        $lessons = DB::table('lesson')->where('topic_id', $tid)->get();
        $total_lesson = Lesson::all()->count();
        return view('lesson_views.create', [
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
            'level_id'    => 'required',
            'unit_id'    => 'required',
            'topic_id'    => 'required',
            'lesson_title'=> 'required',
        ]);
        DB::table('lesson')->insert([
            'title' => $request['lesson_title'],
            'order_no' => $request['order_no'],
            'randomisation' => $request['randomisation'] ?: false,
            'dependency' => $request['dependency'] ?: false,
            'dev_mode' => $request['dev_mode'] ?: false,
            'topic_id' => $request['topic_id'],
            'created_at' => date('Y-m-d H:i:s'),
            'modified_at' => date('Y-m-d H:i:s')
        ]);
        $levels = DB::select('select * from level');
        $units = DB::select('select * from unit');
        $topics = DB::select('select * from topic');
        $lessons = DB::table('lesson')->where('topic_id', $request->topic_id)->get();
        $total_lesson = Lesson::all()->count();
        \Session::flash('flash_message', 'successfully saved.');
        return view('lesson_views.create', [
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
        $lesson = DB::table('lesson')
            ->join('topic', 'lesson.topic_id', '=', 'topic.id')
            ->join('unit', 'topic.unit_id', '=', 'unit.id')
            ->join('level', 'unit.level_id', '=', 'level.id')
            ->select('lesson.*', 'topic.title as ttitle',
            'topic.id as tid', 'unit.title as utitle', 'unit.id as uid', 'level.title as ltitle', 'level.id as lid')
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
         'level_id'    => 'required',
         'unit_id'    => 'required',
         'topic_id'    => 'required',
         'lesson_title'=> 'required',
         ]);
        DB::table('lesson')->where('id', $id)->update([
            'title' => $request['lesson_title'],
            'randomisation' => $request['randomisation'] ?: false,
            'dependency' => $request['dependency'] ?: false,
            'dev_mode' => $request['dev_mode'] ?: false,
            'topic_id' => $request['topic_id'],
            'order_no' => $request['order_no'],
            'created_at' => date('Y-m-d H:i:s'),
            'modified_at' => date('Y-m-d H:i:s')
        ]);
        $levels = DB::select('select * from level');
        $units = DB::select('select * from unit');
        $topics = DB::select('select * from topic');
        $lessons = DB::table('lesson')->where('topic_id', $request->topic_id)->get();
        $total_lesson = Lesson::all()->count();
        return view('lesson_views.create', [
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
        $topic_id = DB::table('lesson')->select('topic_id')->where('id', $id)->first();
        DB::table('lesson')->where('id', $id)->delete();
        $lessons = DB::table('lesson')->where('topic_id', $topic_id->topic_id)->get();
        return view('lesson_views.index', ['levels'=>$levels, 'units'=>$units, 'topics'=>$topics, 'lessons'=>$lessons]);
    }
}
