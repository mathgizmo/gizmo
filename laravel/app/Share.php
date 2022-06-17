<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    protected $table = 'shares';

    protected $fillable = [
        'type', 'item_id', 'sender_id', 'receiver_id', 'accepted', 'accepted_date', 'declined', 'declined_date'
    ];

    public $timestamps = false;

    public function sender() {
        return $this->hasOne('App\Student', 'id', 'sender_id');
    }

    public function receiver() {
        return $this->hasOne('App\Student', 'id', 'receiver_id');
    }
}
