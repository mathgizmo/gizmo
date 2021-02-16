<?php

namespace App\Http\AdminControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Lesson;
use App\Topic;
use App\Level;
use App\Unit;

class LessonController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Lesson::class); // not working!
    }

    public function index(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $query = Lesson::query();
        if ($request['topic_id'] && $request['topic_id'] >= 0) {
            $query->where('topic_id', $request['topic_id']);
        } else if ($request['unit_id'] && $request['unit_id'] >= 0) {
            $query->whereIn('topic_id', function($query) {
                $query->select('id')->from(with(new Topic)->getTable())
                ->where('unit_id', request('unit_id'));
            });
        } else if ($request['level_id']  && $request['level_id'] >= 0) {
            $query->whereIn('topic_id', function($query) {
                $query->select('id')->from(with(new Topic)->getTable())
                ->whereIn('unit_id', function($query) {
                    $query->select('id')->from(with(new Unit)->getTable())
                    ->where('level_id', request('level_id'));
                });
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
        if ($request['sort'] && $request['order']) {
            $query->orderBy($request['sort'], $request['order']);
        }
        return view('lessons.index', [
            'levels' => Level::all(),
            'units' => Unit::all(),
            'topics' => Topic::all(),
            'lessons' => $query->paginate(10)->appends(request()->query()),
            'unit_id' => $request['unit_id'],
            'level_id' => $request['level_id'],
            'topic_id' => $request['topic_id']
        ]);
    }

    public function create()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $lid = "";
        $uid = "";
        $tid = "";
        $levels = DB::select('select * from level');
        $units = DB::select('select * from unit');
        $topics = DB::select('select * from topic');
        $lessons = DB::table('lesson')->where('topic_id', $tid)->get();
        $total_lesson = Lesson::all()->count();
        return view('lessons.create', [
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

    public function store(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
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
            'challenge' => $request['challenge'] ?: false,
            'dev_mode' => $request['dev_mode'] ?: false,
            'topic_id' => $request['topic_id'],
            'created_at' => date('Y-m-d H:i:s'),
            'modified_at' => date('Y-m-d H:i:s')
        ]);
        $level_id = $request->input('level_id');
        $unit_id = $request->input('unit_id');
        $topic_id = $request->input('topic_id');
        return redirect('/lessons?level_id='. $level_id . '&unit_id='. $unit_id. '&topic_id='. $topic_id)->with(array('message'=> 'Created successfully'));
    }

    public function show()
    {
        return "Under Construction";
    }

    public function edit($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
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
        return view('lessons.edit', [
            'lesson' => $lesson,
            'levels' => $levels,
            'units' => $units,
            'topics' => $topics,
            'total_lesson' => $total_lesson
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
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
            'challenge' => $request['challenge'] ?: false,
            'dev_mode' => $request['dev_mode'] ?: false,
            'topic_id' => $request['topic_id'],
            'order_no' => $request['order_no'],
            'created_at' => date('Y-m-d H:i:s'),
            'modified_at' => date('Y-m-d H:i:s')
        ]);
        $level_id = $request->input('level_id');
        $unit_id = $request->input('unit_id');
        $topic_id = $request->input('topic_id');
        return redirect('/lessons?level_id='. $level_id . '&unit_id='. $unit_id. '&topic_id='. $topic_id)->with(array('message'=> 'Updated successfully'));
    }

    public function destroy(Request $request, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        DB::table('lesson')->where('id', $id)->delete();
        $level_id = $request->input('level_id');
        $unit_id = $request->input('unit_id');
        $topic_id = $request->input('topic_id');
        return redirect('/lessons?level_id='. $level_id . '&unit_id='. $unit_id. '&topic_id='. $topic_id)->with(array('message'=> 'Deleted successfully'));
    }
}
