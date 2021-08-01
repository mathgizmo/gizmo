<?php

namespace App\Http\APIControllers;

use App\Tutorial;
use Illuminate\Support\Facades\Auth;

class TutorialController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $query = Tutorial::query();
        if ($user->isTeacher()) {
            $query->where('is_for_teacher', true);
        } else {
            $query->where('is_for_student', true);
        }
        return $this->success($query->orderBy('order_no', 'ASC')->get());
    }
}
