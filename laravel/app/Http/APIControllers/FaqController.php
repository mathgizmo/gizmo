<?php

namespace App\Http\APIControllers;

use App\Faq;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $query = Faq::query();
        if ($user->isTeacher()) {
            $query->where('is_for_teacher', true);
        } else {
            $query->where('is_for_student', true);
        }
        return $this->success($query->orderBy('order_no', 'ASC')->get());
    }
}
