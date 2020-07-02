<?php

namespace App\Http\Controllers;

use App\ReportError;
use Illuminate\Http\Request;

class ReportErrorController extends Controller
{

    public function __construct()
    {
        // $this->authorizeResource(ReportError::class);
    }

    public function index(Request $request, $type)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $query = ReportError::query()->where('declined', $type == 'new' ? 0 : 1);
        if ($request['sort'] && $request['order']) {
            $query->orderBy($request['sort'], $request['order']);
        } else {
           $query->latest();
        }
        $error_reports = $query->paginate(10);
        return view('error_reports.index', compact('error_reports', 'type'));
    }

    public function updateStatus($type, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        if (($model = ReportError::find($id)) == null) {
            return back();
        }
        $model->update([
            'declined' => $type == 'new' ? 0 : 1,
        ]);
        return back();
    }
}
