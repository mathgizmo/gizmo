<?php

namespace App\Http\Controllers;

use App\ReportError;

class ReportErrorController extends Controller
{

    public function index($type)
    {
        $error_reports = ReportError::where('declined', $type == 'new' ? 0 : 1)->latest()->get();

        return view('error_report_views.index', compact('error_reports', 'type'));
    }

    public function updateStatus($type, $id)
    {
        if (($model = ReportError::find($id)) == null) {
            return back();
        }

        $model->update([
            'declined' => $type == 'new' ? 0 : 1,
        ]);

        return back();
    }
}
