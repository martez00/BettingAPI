<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BetSelections extends Model
{
    protected $table = 'bet_selections';
    public $primaryKey = 'id';

    public function bet(){
        return $this->belongsTo('App\Bet');
    }

    public function selection(){
        return $this->belongsTo('App\Selection');
    }
}
