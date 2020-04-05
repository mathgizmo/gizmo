<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Question;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{

    public function __construct()
    {
        // $this->authorizeResource(Question::class); // not working!
    }

    public function index(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin() || auth()->user()->isQuestionsEditor());
        if (count($request->query)) {
            $options = $request->query->all();
            unset($options['_token']);
            session(['options' => $options]);
        } else if (is_array(session('options')) && count(session('options'))) {
            return redirect()->route('question_views.index', session('options'));
        }
        $levels = DB::select('select * from level');
        $reply_modes = DB::select('select * from reply_mode');
        $units = DB::table('unit')->where('level_id', $request->level_id)->get();
        $topics = DB::table('topic')->where('unit_id', $request->unit_id)->get();
        $lessons = DB::table('lesson')->where('topic_id', $request->topic_id)->get();
        $qrmodes = [];
        foreach (DB::select('select * from reply_mode') as $reply_mode) {
            $qrmodes[$reply_mode->code] = $reply_mode->mode;
        }
        $query = DB::table('question')
            ->join('lesson', 'question.lesson_id', '=', 'lesson.id')
            ->join('topic', 'lesson.topic_id', '=', 'topic.id')
            ->join('unit', 'topic.unit_id', '=', 'unit.id')
            ->join('level', 'unit.level_id', '=', 'level.id')
            ->select('question.*', 'lesson.title', 'topic.title as ttitle', 'unit.title as utitle', 'level.title as ltitle', 'lesson.order_no as lesson_order');
        if ($request->has('level_id')) {
            $level_id = $request->level_id;
            $query = $query->where('level_id', $request->level_id);
        } else {
            $level_id = '';
        }
        if ($request->has('unit_id')) {
            $unit_id = $request->unit_id;
            $query = $query->where('unit_id', $request->unit_id);
        } else {
            $unit_id = '';
        }
        if ($request->has('topic_id')) {
            $topic_id = $request->topic_id;
            $query = $query->where('topic_id', $request->topic_id);
        } else {
            $topic_id = '';
        }
        if ($request->has('lesson_id')) {
            $lesson_id = $request->lesson_id;
            $query = $query->where('lesson_id', $request->lesson_id);
        } else {
            $lesson_id = '';
        }
        if ($request->has('question')) {
            $query = $query->where('question', 'like', '%' . $request->question . '%');
        }
        if ($request->has('type')) {
            $query = $query->where('type', 'like', '%' . $request->type . '%');
        }
        if ($request->has('reply_mode')) {
            $query = $query->where('reply_mode', $request->reply_mode);
        }
        if ($request->has('sort') and $request->has('order')) {
            $query = $query->orderBy($request->sort, $request->order);
        } else {
            $query = $query->orderBy('question.id', 'desc');
        }
        $questions = $query->paginate(10)->appends(Input::except('page'));
        return view('question_views.index',
            compact('questions', 'levels', 'units', 'topics', 'lessons', 'level_id', 'unit_id', 'topic_id', 'lesson_id', 'qrmodes', 'reply_modes'));
    }

    public function uploadImage(Request $request)
    {
        if ($request->file('upload')) {
            $funNum = $request->query->get('CKEditorFuncNum', 0);
            $new_file_path = time() . '_' . $request->file('upload')->getClientOriginalName();
            $path = Storage::put(
                'public/uploads/' . $new_file_path,
                file_get_contents($request->file('upload')->getRealPath())
            );
            return '<script type="text/javascript">
            window.parent.CKEDITOR.tools.callFunction(' . $funNum . ', "' . url('/') . '/uploads/' . $new_file_path . '");
            </script>';
        }
    }

    public function create(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin() || auth()->user()->isQuestionsEditor());
        $levels = DB::select('select * from level');
        if ($request->ajax()) {
            $output = '<option value="" selected>Select From ...</option>';
            $toutput = '<option value="" selected>Select From ...</option>';
            $loutput = '<option value="" selected>Select From ...</option>';
            $coutput = '<option value="" selected>No Related Data ...</option>';
            $lid = $request->level_id;
            $request->session()->put('slid', $lid);
            $units = DB::table('unit')->select('id', 'title')->where('level_id', $lid)->get();
            if ($units) {
                foreach ($units as $unit) {
                    $output .= '<option value="' . $unit->id . '">' . $unit->title . '</option>';
                }
                return response()->json($output);
            }
            $uid = $request->unit_id;
            $topics = DB::table('topic')->select('id', 'title')->where('unit_id', $uid)->get();
            if ($topics) {
                foreach ($topics as $topic) {
                    $toutput .= '<option value="' . $topic->id . '">' . $topic->title . '</option>';
                }
                return response()->json($toutput);
            }
            $tid = $request->topic_id;
            $lessons = DB::table('lesson')->select('id', 'title')->where('topic_id', $tid)->get();
            if ($lessons) {
                foreach ($lessons as $lesson) {
                    $loutput .= '<option value="' . $lesson->id . '">' . $lesson->title . '</option>';
                }
                return response()->json($loutput);
            } else {
                return response()->json($coutput);
            }
        };
        $lid = $request->session()->get('slid');
        $uid = "";
        $tid = "";
        $lsnid = "";
        $units = DB::table('unit')->select('id', 'title')->get();
        $topics = DB::table('topic')->select('id', 'title')->get();
        $lessons = DB::table('lesson')->select('id', 'title')->get();
        $qrmodes = DB::select('select * from reply_mode');
        $preview_url = Config::get('app.preview_url');
        return view('question_views.create',
            ['levels' => $levels, 'qrmodes' => $qrmodes, 'units' => $units, 'topics' => $topics, 'lessons' => $lessons, 'lid' => $lid, 'uid' => $uid, 'tid' => $tid, 'lsnid' => $lsnid, 'preview_url' => $preview_url]);
    }

    public function store(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin() || auth()->user()->isQuestionsEditor());
        $lid = $request->level_id;
        $uid = $request->unit_id;
        $tid = $request->topic_id;
        $lesson_id = $request->lesson_id;
        $this->validate($request, [
            'level_id' => 'required',
            'unit_id' => 'required',
            'topic_id' => 'required',
            'lesson_id' => 'required',
            'reply_mode' => 'required',
            'question' => 'required',
            'answer' => 'required|array|min:1|max:6',
            'answer.*' => 'required|string'
        ]);
        $collectionQuestion = collect(['lesson_id' => $request['lesson_id'], 'reply_mode' => $request['reply_mode'], 'question' => $request['question']]);
        $collectionQuestion = $collectionQuestion->merge([
            'explanation' => $request['explanation'],
            'created_at' => date('Y-m-d H:i:s'),
            'conversion' => $request['rounding'] == 2,
            'rounding' => $request['rounding'] == 1,
            'question_order' => $request['question_order'] ?: false,
            'answers_round' => $request['answers_round'] ?: 0,
            'modified_at' => date('Y-m-d H:i:s')
        ]);
        $question = Question::create($collectionQuestion->all());
        $type = $request['reply_mode'];
        $iterations = str_replace(['general', 'FB', 'TF', 'mcq', 'order', 'mcqms'],
            [1, 6, 1, 6, 6, 6], $type);
        for ($i = 0; $i < ($iterations > count($request->answer) ? count($request->answer) : $iterations); $i++) {
            $is_correct = in_array($i, $request->is_correct) ? 1 : 0;
            Answer::create([
                'question_id' => $question->id,
                'value' => $request->answer[$i],
                'answer_order' => $i,
                'is_correct' => $is_correct,
            ]);
        }
        $levels = DB::select('select * from level');
        $units = DB::table('unit')->select('id', 'title')->where('level_id', $lid)->get();
        $topics = DB::table('topic')->select('id', 'title')->where('unit_id', $uid)->get();
        $lessons = DB::table('lesson')->select('id', 'title')->where('topic_id', $tid)->get();
        $qrmodes = DB::select('select * from reply_mode');
        \Session::flash('flash_message', 'successfully saved.');
        $preview_url = Config::get('app.preview_url');
        return view('question_views.create', ['levels' => $levels,
            'qrmodes' => $qrmodes, 'units' => $units, 'topics' => $topics, 'lessons' => $lessons, 'lid' => $lid, 'uid' => $uid, 'tid' => $tid, 'lsnid' => $lesson_id, 'preview_url' => $preview_url])->withInput($request->all());
    }

    public function show($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin() || auth()->user()->isQuestionsEditor());
        $question = DB::table('question')
            ->join('lesson', 'question.lesson_id', '=', 'lesson.id')
            ->join('topic', 'lesson.topic_id', '=', 'topic.id')
            ->join('unit', 'topic.unit_id', '=', 'unit.id')
            ->join('level', 'unit.level_id', '=', 'level.id')
            ->select('question.*', 'lesson.title', 'topic.title as ttitle', 'unit.title as utitle', 'level.title as ltitle')
            ->where('question.id', '=', $id)->first();
        $answers = DB::select('select * from answer where question_id = ' . $id);
        return view('question_views.show', ['question' => $question, 'answers' => $answers]);
    }

    public function edit($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin() || auth()->user()->isQuestionsEditor());
        $question = DB::table('question')
            ->join('lesson', 'question.lesson_id', '=', 'lesson.id')
            ->join('topic', 'lesson.topic_id', '=', 'topic.id')
            ->join('unit', 'topic.unit_id', '=', 'unit.id')
            ->join('level', 'unit.level_id', '=', 'level.id')
            ->select('question.*', 'lesson.title', 'topic.title as ttitle',
                'topic.id as tid', 'unit.title as utitle', 'unit.id as uid', 'level.title as ltitle', 'level.id as lid')
            ->where('question.id', '=', $id)->first();
        $answers = DB::select('select * from answer where question_id = ' . $id);
        $levels = DB::select('select * from level');
        $units = DB::table('unit')->select('id', 'title')->where('level_id', $question->lid)->get();
        $topics = DB::table('topic')->select('id', 'title')->where('unit_id', $question->uid)->get();
        $lessons = DB::table('lesson')->select('id', 'title')->where('topic_id', $question->tid)->get();
        $qrmodes = DB::select('select * from reply_mode');
        $preview_url = Config::get('app.preview_url');
        return view('question_views.edit', ['question' => $question, 'levels' => $levels,
            'units' => $units, 'topics' => $topics, 'lessons' => $lessons, 'qrmodes' => $qrmodes, 'answers' => $answers, 'preview_url' => $preview_url]);
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin() || auth()->user()->isQuestionsEditor());
        $questionID = $id;
        $this->validate($request, [
            'level_id' => 'required',
            'unit_id' => 'required',
            'topic_id' => 'required',
            'lesson_id' => 'required',
            'question' => 'required',
            'reply_mode' => 'required',
            'answer' => 'required|array|min:1|max:6',
            'answer.*' => 'required|string',
        ]);
        $collectionQuestion = collect([
            'lesson_id' => $request['lesson_id'],
            'reply_mode' => $request['reply_mode'],
            'question' => $request['question']
        ]);
        $collectionQuestion = $collectionQuestion
            ->merge([
                'explanation' => $request['explanation'],
                'conversion' => $request['rounding'] == 2,
                'rounding' => $request['rounding'] == 1,
                'question_order' => $request['question_order'] ?: false,
                'answers_round' => $request['answers_round'] ?: 0,
                'created_at' => date('Y-m-d H:i:s'),
                'modified_at' => date('Y-m-d H:i:s')
            ]);
        if ($request['_type'] == 'new') {
            $questionID = DB::table('question')->insertGetId($collectionQuestion->all());
        } else {
            DB::table('question')
                ->where('id', $questionID)
                ->update($collectionQuestion->all());
            Question::find($questionID)->answers()->delete();
        }
        $type = $request['reply_mode'];
        $iterations = str_replace(['general', 'FB', 'TF', 'mcq', 'order', 'mcqms'],
            [1, 6, 1, 6, 6, 6], $type);
        for ($i = 0; $i < ($iterations > count($request->answer) ? count($request->answer) : $iterations); $i++) {
            $is_correct = in_array($i, $request->is_correct) ? 1 : 0;
            Answer::create([
                'question_id' => $questionID,
                'value' => $request->answer[$i],
                'answer_order' => $i,
                'is_correct' => $is_correct,
            ]);
        }
        if ($request['_type'] == 'new') {
            $question = DB::table('question')
                ->join('lesson', 'question.lesson_id', '=', 'lesson.id')
                ->join('topic', 'lesson.topic_id', '=', 'topic.id')
                ->join('unit', 'topic.unit_id', '=', 'unit.id')
                ->join('level', 'unit.level_id', '=', 'level.id')
                ->select('question.*', 'lesson.title', 'topic.title as ttitle',
                    'topic.id as tid', 'unit.title as utitle', 'unit.id as uid', 'level.title as ltitle', 'level.id as lid')
                ->where('question.id', '=', $questionID)->first();
            $answers = DB::select('select * from answer where question_id = ' . $questionID);
            $levels = DB::select('select * from level');
            $units = DB::table('unit')->select('id', 'title')
                ->where('level_id', $question->lid)->get();
            $topics = DB::table('topic')->select('id', 'title')
                ->where('unit_id', $question->uid)->get();
            $lessons = DB::table('lesson')->select('id', 'title')
                ->where('topic_id', $question->tid)->get();
            $qrmodes = DB::select('select * from reply_mode');
            $preview_url = Config::get('app.preview_url');
            return view('question_views.edit', ['question' => $question,
                'levels' => $levels, 'units' => $units, 'topics' => $topics,
                'lessons' => $lessons, 'qrmodes' => $qrmodes, 'answers' => $answers,
                'preview_url' => $preview_url]);
        } else {
            return redirect(route('question_views.index'));
        }
    }

    public function destroy($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        try {
            DB::table('question')->where('id', $id)->delete();
        } finally {
            return redirect()->route('question_views.index');
        }
    }

}
