<?php

namespace App\Http\Controllers;

use App\ReportError;

use Illuminate\Http\Request;

class ReportErrorController extends Controller
{

    public function index(Request $request, $type)
    {
        if ($request->has('sort') and $request->has('order')) {
            $error_reports = ReportError::where('declined', $type == 'new' ? 0 : 1)->orderBy($request->sort, $request->order)->latest()->get();
        } else {
            $error_reports = ReportError::where('declined', $type == 'new' ? 0 : 1)->latest()->get();
        }
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
