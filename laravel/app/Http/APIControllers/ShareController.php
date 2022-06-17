<?php

namespace App\Http\APIControllers;

use App\Application;
use App\ClassOfStudents;
use App\Student;
use App\Share;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShareController extends Controller
{
    public function acceptNewShare(Request $request, $type, $item_id) {
        $user = Auth::user();
        $user_id = $user->id;

        $item = Share::where('type', $type)
            ->where('receiver_id', $user_id)
            ->where('item_id', $item_id)
            ->where('accepted', 0)
            ->where('declined', 0)
            ->orderBy('date', 'DESC')
            ->first();

        if($item) {
            $new_object = null;
            if($item->type == 'classroom') {
                $object = ClassOfStudents::where('classes.id', $item->item_id)
                    ->where('classes.teacher_id', $item->sender_id)
                    ->first();
                $new_object = $object->replicateWithRelations($user_id);
            }else{
                $object = Application::where('id', $item->item_id)
                    ->where('type', $item->type)->get()
                    ->where('teacher_id', $item->sender_id)
                    ->first();
                $new_object = $object->replicateWithRelations($user_id);
            }
            if($new_object) {
                $item->accepted = 1;
                $item->accepted_date = Carbon::now()->toDateTimeString();
                $item->save();

                return $this->success('Ok.');
            }
        }

        return $this->error('Error.');
    }

    public function declineNewShare(Request $request, $type, $item_id) {
        $user = Auth::user();
        $user_id = $user->id;

        $item = Share::where('type', $type)
            ->where('receiver_id', $user_id)
            ->where('item_id', $item_id)
            ->where('accepted', 0)
            ->where('declined', 0)
            ->orderBy('date', 'DESC')
            ->first();

        if($item) {
            $item->declined = 1;
            $item->declined_date = Carbon::now()->toDateTimeString();
            $item->save();

            return $this->success('Ok.');
        }

        return $this->error('Error.');
    }

    public function getNewShare(Request $request, $type) {
        $user = Auth::user();
        $user_id = $user->id;

        $item = Share::where('type', $type)
            ->where('receiver_id', $user_id)
            ->where('accepted', 0)
            ->where('declined', 0)
            ->orderBy('date', 'DESC')
            ->first();

        if($item) {
            if($item->type == 'classroom') {
                $object = ClassOfStudents::where('classes.id', $item->item_id)
                    ->where('classes.teacher_id', $item->sender_id)
                    ->first();
            }else{
                $object = Application::where('id', $item->item_id)
                    ->where('type', $item->type)->get()
                    ->where('teacher_id', $item->sender_id)
                    ->first();
            }
            if($item->sender && $object) {
                $item->{ $type } = $object;
            }else{
                $item = null;
            }
        }

        return $this->success([
            'item' => $item
        ]);
    }

    public function getShared(Request $request, $type, $item_id) {

        $user = Auth::user();
        $user_id = $user->id;

        $items = Share::where('type', $type)
            ->where('item_id', $item_id)
            ->where('sender_id', $user_id)
            ->orderBy('declined', 'ASC')
            ->orderBy('accepted', 'ASC')
            ->get();

        $shares = [];
        foreach ($items as $item) {
            if($item->receiver) {
                $shares[] = $item;
            }
        }

        $not_available = $items->pluck('receiver_id')->toArray();
        $available = Student::where('is_teacher', true)
            ->whereNotIn('students.id', $not_available)
            ->where('id', '<>', $user_id)
            ->orderBy('email', 'ASC')->get();
        $available_teachers = array_values($available->toArray());
        return $this->success([
            'shares' => $shares,
            'available_teachers' => $available_teachers
        ]);
    }



    public function addShared(Request $request, $type, $class_id) {
        if($request['teachers']) {
            $user = Auth::user();
            $user_id = $user->id;
            foreach ($request['teachers'] as $teacher_id){
                $teacher = Student::where('id', $teacher_id)->where('is_teacher', true)->first();
                if (!$teacher) {
                    return $this->error('Teacher not found!', 404);
                }
                $exists = Share::where('type', $type)
                    ->where('item_id', $class_id)
                    ->where('receiver_id', $teacher_id)
                    ->where('accepted', 0)
                    ->where('declined', 0)
                    ->first();
                if (!$exists) {
                    Share::create([
                        'type' => $type,
                        'item_id' => $class_id,
                        'sender_id' => $user_id,
                        'receiver_id' => $teacher_id
                    ]);
                }
            }

            return $this->getShared($request, $type, $class_id);
        }
        return $this->error('Error.');
    }


    public function deleteShared($type, $class_id, $receiver_id) {
        $user = Auth::user();
        $user_id = $user->id;
        $share = Share::where('type', $type)
            ->where('item_id', $class_id)
            ->where('sender_id', $user_id)
            ->where('receiver_id', $receiver_id)
            ->where('accepted', 0)
            ->where('declined', 0)
            ->first();
        if ($share) {
            $share->delete();
            return $this->success('Ok.');
        }
        return $this->error('Error.');
    }

}
