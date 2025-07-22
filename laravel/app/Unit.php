<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'unit';
    protected $fillable = [
        'title', 'description', 'dependency', 'dev_mode', 'level_id', 'order_no', 'created_at', 'modified_at'
    ];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public function topics()
    {
        return $this->hasMany('App\Topic', 'unit_id', 'id');
    }

    public function level()
    {
        return $this->belongsTo('App\Level', 'level_id');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag', 'tag_unit', 'unit_id', 'tag_id');
    }
}
