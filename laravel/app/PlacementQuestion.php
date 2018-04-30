<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlacementQuestion extends Model
{
    protected $table = 'placement_questions';

    public function unit()
    {
        return $this->belongsTo('App\Unit', 'unit_id');
    }

    /**
     * Create a new PlacementQuestions instance.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $placement = new PlacementQuestion;
        $placement->order = $request->order;
        $placement->question = $request->question;
        $placement->is_active = $request->is_active ?: 1;
        $unit = App\Unit::find($request['unit_id']);
        $placement->unit()->associate($unit);
        $placement->save();
    }

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
