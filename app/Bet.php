<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    public $primaryKey = 'id';
    public $timestamps = false;
    protected $table = 'bets';

    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function betSelections()
    {
        return $this->hasMany('App\BetSelections');
    }
}
