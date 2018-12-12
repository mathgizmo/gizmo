<?php

namespace App\Http\Controllers;

use App\ReportError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ReportErrorController extends Controller
{

    /**
     * @param Request $request
     * @param $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, $type)
    {
        $query = ReportError::query()->where('declined', $type == 'new' ? 0 : 1);
        if($request->has('sort') and $request->has('order')) {
            $query->orderBy(request('sort'), request('order'));
        } else {
           $query->latest();
        }
        $error_reports = $query->paginate(10)->appends(Input::except('page'));
        return view('error_report_views.index', compact('error_reports', 'type'));
    }

    /**
     * @param $type
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
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
