<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'key', 'label', 'value',
    ];

    public static function getValueByKey($key) {
        return self::where('key', $key)->first()->value;
    }
}
