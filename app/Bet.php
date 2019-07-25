<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    protected $table = 'bets';
    public $primaryKey = 'id';
    public $timestamps = false;

    public function user(){
        return $this->hasOne('App\User');
    }
    public function betSelections(){
        return $this->hasMany('App\BetSelections');
    }
}
