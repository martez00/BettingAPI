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
        return $this->belongsTo('App\Bet', 'bet_id');
    }

    public function selection()
    {
        return $this->belongsTo('App\Selection', 'selection_id');
    }
}
