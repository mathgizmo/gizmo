<?php

namespace App\Http\APIControllers;

use App\Application;
use App\ClassOfStudents;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use JWTAuth;

class ClassController extends Controller
{

	private $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function all() {
        return $this->success([
            'items' => array_values(ClassOfStudents::where('teacher_id', $this->user->id)->get()->toArray())
        ]);
    }

    public function store() {
        try {
            return $this->success([
                'item' => ClassOfStudents::create([
                    'teacher_id' => $this->user->id,
                    'name' => request('name'),
                    'subscription_type' => request('subscription_type') ?: 'open',
                    'invitations' => request('invitations')
                ])
            ]);
        } catch (\Exception $e) {
            return $this->error('Error.');
        }
    }

    public function update($class_id) {
        try {
            $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
            if ($class) {
                if (request()->has('name')) {
                    $class->name = request('name');
                }
                if (request()->has('subscription_type')) {
                    $class->subscription_type = request('subscription_type');
                }
                if (request()->has('invitations')) {
                    $class->invitations = request('invitations');
                }
                $class->save();
                return $this->success(['item' => $class]);
            }
        } catch (\Exception $e) {}
        return $this->error('Error.');
    }

    public function delete($class_id) {
        if ($class_id == 1) {
            abort('403', 'Default class can\'t be deleted!');
        }
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            $class->delete();
            return $this->success('Ok.');
        }
        return $this->error('Error.');
    }

    public function getStudents($class_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            $items = $class->students()->get(['students.id', 'students.name', 'students.first_name', 'students.last_name', 'students.email'])->toArray();
            return $this->success(['items' => array_values($items)]);
        }
        return $this->error('Error.');
    }

    public function getAssignments($class_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            $items = $class->applications()->get();
            foreach ($items as $item) {
                $item->icon = $item->icon();
                $item->due_date = $item->getDueDate($class->id);
            }
            $available = Application::where('teacher_id', $this->user->id)
                ->whereNotIn('id', $items->pluck('id')->toArray())->orderBy('name')->get();
            foreach ($available as $item) {
                $item->icon = $item->icon();
            }
            return $this->success([
                'assignments' => array_values($items->sortBy('due_date')->toArray()),
                'available_assignments' => array_values($available->toArray())
            ]);
        }
        return $this->error('Error.');
    }

    public function addAssignmentToClass($class_id, $app_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        $exists = DB::table('classes_applications')->where('class_id', $class_id)->where('app_id', $app_id)->first();
        if ($class && !$exists) {
            DB::table('classes_applications')->insert([
                'class_id' => $class->id,
                'app_id' => $app_id
                // 'due_date' => null
            ]);
            return $this->success('Ok.');
        }
        return $this->error('Error.');
    }

    public function changeAssignmentDueDate($class_id, $app_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            DB::table('classes_applications')->where('class_id', $class->id)->where('app_id', $app_id)
                ->update(['due_date' => request('due_date')]);
            return $this->success('Ok.');
        }
        return $this->error('Error.');
    }

    public function deleteAssignmentFromClass($class_id, $app_id) {
        $class = ClassOfStudents::where('id', $class_id)->where('teacher_id', $this->user->id)->first();
        if ($class) {
            DB::table('classes_applications')->where('class_id', $class->id)->where('app_id', $app_id)
                ->delete();
            return $this->success('Ok.');
        }
        return $this->error('Error.');
    }
}
