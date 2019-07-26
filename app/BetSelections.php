<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BetSelections extends Model
{
    public $primaryKey = 'id';
    public $timestamps = false;
    protected $table = 'bet_selections';

    public function bet()
    {
        return $this->hasOne('App\Bet');
    }

    public function selection()
    {
        return $this->hasOne('App\Selection');
    }
}
