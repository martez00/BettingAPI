<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    protected $table = 'bets';
    public $primaryKey = 'id';

    public function user(){
        return $this->belongsTo('App\User');
    }
}
