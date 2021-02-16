<?php

namespace App\Http\AdminControllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Unit;
use App\Level;

class UnitController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Unit::class); // not working!
    }

    public function index(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $query = Unit::query();
        if ($request['level_id']) {
            $query->where('level_id', $request['level_id']);
        }
        if ($request['id']) {
            $query->where('id', $request['id']);
        }
        if ($request['order_no']) {
            $query->where('order_no', $request['order_no']);
        }
        if ($request['title']) {
            $query->where('title', 'LIKE', '%' . $request['title'] . '%');
        }
        if ($request['sort'] && $request['order']) {
            $query->orderBy($request['sort'], $request['order']);
        }
        return view('units.index', [
            'levels' => Level::all(),
            'units' => $query->paginate(10)->appends(request()->query()),
            'level_id' => $request['level_id']
        ]);
    }

    public function create()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $lid = "";
        $levels = DB::select('select * from level');
        $units = DB::table('unit')->where('level_id', $lid)->get();
        $total_unit = Unit::all()->count();
        return view('units.create', [
            'levels' => $levels,
            'units' => $units,
            'lid' => $lid,
            'total_unit' => $total_unit,
        ]);
    }

    public function store(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        // Store topic title and unit_id into topic table
        $this->validate($request, [
            'level_id' => 'required',
            'unit_title' => 'required',
        ]);
        DB::table('unit')->insert([
            'title' => $request['unit_title'],
            'description' => $request['description'],
            'dependency' => $request['dependency'] ?: false,
            'dev_mode' => $request['dev_mode'] ?: false,
            'level_id' => $request['level_id'],
            'order_no' => $request['order_no'],
            'created_at' => date('Y-m-d H:i:s'),
            'modified_at' => date('Y-m-d H:i:s')
        ]);
        $level_id = $request->input('level_id');
        return redirect('/units?level_id=' . $level_id)->with(array('message' => 'Created successfully'));
    }

    public function show()
    {
        return "Under Construction";
    }

    public function edit($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $unit = DB::table('unit')
            ->join('level', 'unit.level_id', '=', 'level.id')
            ->select('unit.*', 'level.title as ltitle', 'level.id as lid')
            ->where('unit.id', '=', $id)->first();
        $levels = DB::select('select * from level');
        $total_unit = Unit::all()->count();
        return view('units.edit', [
            'levels' => $levels,
            'unit' => $unit,
            'total_unit' => $total_unit,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
            'level_id' => 'required',
            'unit_title' => 'required'
        ]);
        DB::table('unit')->where('id', $id)->update([
            'title' => $request['unit_title'],
            'description' => $request['description'],
            'dependency' => $request['dependency'] ?: false,
            'dev_mode' => $request['dev_mode'] ?: false,
            'level_id' => $request['level_id'],
            'order_no' => $request['order_no'],
            'created_at' => date('Y-m-d H:i:s'),
            'modified_at' => date('Y-m-d H:i:s')
        ]);
        $level_id = $request->input('level_id');
        return redirect('/units?level_id=' . $level_id)->with(array('message' => 'Updated successfully'));
    }

    public function destroy(Request $request, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $level_id = $request->input('level_id');
        Unit::where('id', $id)->delete();
        return redirect('/units?level_id=' . $level_id)->with(array('message' => 'Deleted successfully'));
    }
}
