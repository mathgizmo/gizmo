<?php

namespace App\Http\APIControllers;

use App\ClassThread;
use App\ClassOfStudents;
use App\ClassThreadReply;
use App\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClassThreadController extends Controller
{

    private $user;

    public function __construct()
    {
        try {
            $this->user = JWTAuth::parseToken()->authenticate();
            if (!$this->user) {
                abort(401, 'Unauthorized!');
            }
        } catch (\Exception $e) {
            abort(401, 'Unauthorized!');
        }
    }

    public function index(Request $request, $class_id) {
        $class = $this->getClass($class_id);
        if (!$class) { return$this->error('Not Found!', 404); }
        $threads = ClassThread::where('class_id', $class_id)->orderBy('updated_at', 'DESC')->get();
        $threads = $threads->map(function ($thread) {
            $student = Student::where('id', $thread->student_id)->first();
            return [
                'id' => $thread->id,
                'class_id' => $thread->class_id,
                'student_id' => $thread->student_id,
                'student_email' => $student ? $student->email : null,
                'student_name' => $student ? ($student->first_name . ' ' . $student->last_name) : $thread->student_id,
                'title' => $thread->title,
                'message' => $thread->message,
                'created_at' => Carbon::parse($thread->created_at)->format('Y-m-d g:i A'),
                'updated_at' => Carbon::parse($thread->updated_at)->format('Y-m-d g:i A'),
                'replies_count' => $thread->getRepliesCount()
            ];
        });
        return $this->success([
            'class' => $class,
            'items' => array_values($threads->toArray())
        ]);
    }

    public function store(Request $request, $class_id) {
        $class = $this->getClass($class_id);
        if (!$class) { return$this->error('Not Found!', 404); }
        try {
            $item = ClassThread::create([
                'class_id' => $class_id,
                'student_id' => $this->user->id,
                'title' => $request['title'],
                'message' => $request['message']
            ]);
            return $this->success([
                'item' => (object) [
                    'id' => $item->id,
                    'class_id' => $item->class_id,
                    'student_id' => $item->student_id,
                    'student_email' => $this->user->email,
                    'student_name' => $this->user->first_name . ' ' . $this->user->last_name,
                    'title' => $item->title,
                    'message' => $item->message,
                    'created_at' => Carbon::parse($item->created_at)->format('Y-m-d g:i A'),
                    'updated_at' => Carbon::parse($item->updated_at)->format('Y-m-d g:i A'),
                    'replies_count' => $item->getRepliesCount()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->error('Error.', 404);
        }
    }

    public function update(Request $request, $class_id, $thread_id) {
        $class = $this->getClass($class_id);
        if (!$class) { return$this->error('Not Found!', 404); }
        try {
            $user = $this->user;
            $thread = ClassThread::where('id', $thread_id)->where(function ($q) use ($user) {
                if (!$user->isTeacher()) {
                    $q->where('student_id', $user->id);
                }
            })->first();
            if ($thread) {
                $thread->title = $request['title'];
                $thread->message = $request['message'];
                $thread->save();
                return $this->success(['item' => $thread]);
            }
        } catch (\Exception $e) {}
        return $this->error('Error.', 404);
    }

    public function destroy(Request $request, $class_id, $thread_id) {
        $class = $this->getClass($class_id);
        if (!$class) { return$this->error('Not Found!', 404); }
        try {
            $user = $this->user;
            $thread = ClassThread::where('id', $thread_id)->where(function ($q) use ($user) {
                if (!$user->isTeacher()) {
                    $q->where('student_id', $user->id);
                }
            })->first();
            if ($thread) {
                $thread->replies()->delete();
                $thread->delete();
                return $this->success(['item' => $thread]);
            }
        } catch (\Exception $e) {}
        return $this->error('Error.', 404);
    }

    public function getReplies(Request $request, $class_id, $thread_id) {
        $class = $this->getClass($class_id);
        if (!$class) { return$this->error('Not Found!', 404); }
        $items = ClassThreadReply::where('thread_id', $thread_id)
            ->orderBy('updated_at', 'DESC')->get();
        $items = $items->map(function ($item) {
            $student = Student::where('id', $item->student_id)->first();
            return [
                'id' => $item->id,
                'thread_id' => $item->thread_id,
                'student_id' => $item->student_id,
                'student_email' => $student ? $student->email : null,
                'student_name' => $student ? ($student->first_name . ' ' . $student->last_name) : $item->student_id,
                'message' => $item->message,
                'created_at' => Carbon::parse($item->created_at)->format('Y-m-d g:i A'),
                'updated_at' => Carbon::parse($item->updated_at)->format('Y-m-d g:i A'),
            ];
        });
        return $this->success([
            'items' => array_values($items->toArray())
        ]);
    }

    public function storeReply(Request $request, $class_id, $thread_id) {
        $class = $this->getClass($class_id);
        if (!$class) { return$this->error('Not Found!', 404); }
        try {
            $item = ClassThreadReply::create([
                'thread_id' => $thread_id,
                'student_id' => $this->user->id,
                'parent_id' => $request['parent_id'] ?: null,
                'message' => $request['message']
            ]);
            return $this->success([
                'item' => (object) [
                    'id' => $item->id,
                    'thread_id' => $item->thread_id,
                    'student_id' => $item->student_id,
                    'student_email' => $this->user->email,
                    'student_name' => $this->user->first_name . ' ' . $this->user->last_name,
                    'message' => $item->message,
                    'created_at' => Carbon::parse($item->created_at)->format('Y-m-d g:i A'),
                    'updated_at' => Carbon::parse($item->updated_at)->format('Y-m-d g:i A'),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->error('Error.', 404);
        }
    }

    public function updateReply(Request $request, $class_id, $thread_id, $reply_id) {
        $class = $this->getClass($class_id);
        if (!$class) { return$this->error('Not Found!', 404); }
        try {
            $user = $this->user;
            $reply = ClassThreadReply::where('id', $reply_id)->where(function ($q) use ($user) {
                if (!$user->isTeacher()) {
                    $q->where('student_id', $user->id);
                }
            })->first();
            if ($reply) {
                $reply->message = $request['message'];
                $reply->save();
                return $this->success(['item' => $reply]);
            }
        } catch (\Exception $e) {}
        return $this->error('Error.', 404);
    }

    public function destroyReply(Request $request, $class_id, $thread_id, $reply_id) {
        $class = $this->getClass($class_id);
        if (!$class) { return$this->error('Not Found!', 404);}
        try {
            $user = $this->user;
            $reply = ClassThreadReply::where('id', $reply_id)->where(function ($q) use ($user) {
                if (!$user->isTeacher()) {
                    $q->where('student_id', $user->id);
                }
            })->first();
            if ($reply) {
                $reply->delete();
                return $this->success(['item' => $reply]);
            }
        } catch (\Exception $e) {}
        return $this->error('Error.', 404);
    }

    private function getClass($class_id) {
        $user_id = $this->user->id;
        if ($this->user->isTeacher()) {
            $class = ClassOfStudents::where('id', $class_id)
                ->where(function ($q1) use($user_id) {
                    $q1->where('classes.teacher_id', $user_id)
                        ->orWhereHas('teachers', function ($q2) use($user_id) {
                            $q2->where('students.id', $user_id);
                        });
                })
                ->first();
        } else {
            $class = ClassOfStudents::whereHas('students', function ($q) use ($user_id) {
                $q->where('students.id', $user_id);
            })->where('classes.id', $class_id)->first();
        }
        return $class;
    }
}
