<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\PlacementQuestion;
use App\Unit;

use App\Http\Requests;

class PlacementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $placements = PlacementQuestion::with('unit')->get();
        return view('placement_views.index',['placements'=>$placements]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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
        \Session::flash('flash_message','successfully saved.');
        return view('placement_views.index',['placements'=>$placements]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return "Under Construction";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $placement = PlacementQuestion::find($id);
        $total_placements = PlacementQuestion::all()->count();
        $units = Unit::all();

        return view('placement_views.edit', [
            'placement' => $placement,
            'total_placements' => $total_placements,
            'units' => $units
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
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
            ->with( array('message'=> 'Updated successfully') );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PlacementQuestion::where('id', $id)->delete();
        return redirect('/placement_views')
            ->with( array('message'=> 'Deleted successfully') );
    }
}
