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
        return $this->belongsTo('App\User', 'user_id');
    }

    public function betSelections()
    {
        return $this->hasMany('App\BetSelections', 'bet_id');
    }
}
