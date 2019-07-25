<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BetSelections extends Model
{
    protected $table = 'bet_selections';
    public $primaryKey = 'id';
    public $timestamps = false;

    public function bet(){
        return $this->hasOne('App\Bet');
    }

    public function selection(){
        return $this->hasOne('App\Selection');
    }
}
