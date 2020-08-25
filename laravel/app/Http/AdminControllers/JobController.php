<?php

namespace App\Http\Controllers;

use App\StudentsTrackingQuestion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Dashboard;

class JobController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Dashboard::class); // not working!
    }

    public function deleteOldAnswersStatistics() {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        $date = Carbon::now()->subDays(100)->toDateString();
        StudentsTrackingQuestion::where('created_at', '<', $date)->delete();
        return response()->json(['status' => 'success'], 200);
    }

}
