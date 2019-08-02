<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Selection extends Model
{
    public $primaryKey = 'id';
    public $timestamps = false;
    protected $table = 'selections';

    public function selectionBets()
    {
        return $this->hasMany('App\BetSelections', 'selection_id');
    }
}
