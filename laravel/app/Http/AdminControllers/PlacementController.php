<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PlacementQuestion;
use App\Unit;

class PlacementController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(PlacementQuestion::class); // not working!
    }

    public function index(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        if ($request->has('sort') and $request->has('order')) {
            $placements = PlacementQuestion::with('unit')
                ->orderBy($request->sort, $request->order)->get();
        } else {
            $placements = PlacementQuestion::with('unit')->get();
        }
        return view('placement_views.index', ['placements'=>$placements]);
    }

    public function create()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $total_placements = PlacementQuestion::all()->count();
        $placements = PlacementQuestion::with('unit')->get();
        $units = Unit::all();
        $lid = "";
        return view('placement_views.create', array(
            'placements' => $placements,
            'total_placements' => $total_placements,
            'units' => $units,
            'lid' => $lid
        ));
    }

    public function store(Request $request)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
         'unit_id' => 'required'
         ]);
        $placement = new PlacementQuestion;
        $placement->order = $request['order'];
        $placement->question = $request['question'];
        $placement->is_active = $request['is_active'] ?: 0;
        $unit = Unit::find($request['unit_id']);
        $placement->unit()->associate($unit);
        $placement->save();
        $placements = PlacementQuestion::with('unit')->get();
        \Session::flash('flash_message', 'successfully saved.');
        return view('placement_views.index', ['placements'=>$placements]);
    }

    public function show()
    {
        return "Under Construction";
    }

    public function edit($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $placement = PlacementQuestion::find($id);
        $total_placements = PlacementQuestion::all()->count();
        $units = Unit::all();
        return view('placement_views.edit', [
            'placement' => $placement,
            'total_placements' => $total_placements,
            'units' => $units
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        $this->validate($request, [
         'unit_id' => 'required'
         ]);
        $placement = PlacementQuestion::find($id);
        $placement->order = $request['order'];
        $placement->question = $request['question'];
        $placement->is_active = $request['is_active'] ?: 0;
        $unit = Unit::find($request['unit_id']);
        $placement->unit()->associate($unit);
        $placement->save();
        return redirect('/placement_views')
            ->with(array('message'=> 'Updated successfully'));
    }

    public function destroy($id)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin());
        PlacementQuestion::where('id', $id)->delete();
        return redirect('/placement_views')
            ->with(array('message'=> 'Deleted successfully'));
    }
}
