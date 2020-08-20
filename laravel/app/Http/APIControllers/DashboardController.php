<?php

namespace App\Http\APIControllers;

use App\Dashboard;

class DashboardController extends Controller
{
    public function getDashboards()
    {
        return $this->success(Dashboard::orderBy('order_no', 'ASC')->get());
    }
}
