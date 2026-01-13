<?php

namespace App\Http\APIControllers;

use App\Dashboard;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $query = Dashboard::query();
        if ($user->isTeacher()) {
            $query->where('is_for_teacher', true);
        } else {
            $query->where('is_for_student', true);
        }
        $dashboards = $query->orderBy('order_no', 'ASC')->get();
        
        return $this->success([
            'dashboards' => $dashboards,
            'has_donated' => $user->has_donated ?? false
        ]);
    }

}
