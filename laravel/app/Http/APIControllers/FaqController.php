<?php

namespace App\Http\APIControllers;

use App\Faq;
use App\Student;
use Tymon\JWTAuth\Facades\JWTAuth;

class FaqController extends Controller
{
    private $student;

    public function __construct()
    {
        try {
            $auth_user = JWTAuth::parseToken()->authenticate();
            if (!$auth_user) {
                abort(401, 'Unauthorized!');
            }
            $this->student = Student::find($auth_user->id);
            if (!$this->student) {
                abort(401, 'Unauthorized!');
            }
        } catch (\Exception $e) {
            abort(401, 'Unauthorized!');
        }
    }

    public function index()
    {
        $query = Faq::query();
        if ($this->student->isTeacher()) {
            $query->where('is_for_teacher', true);
        } else {
            $query->where('is_for_student', true);
        }
        return $this->success($query->orderBy('order_no', 'ASC')->get());
    }
}
