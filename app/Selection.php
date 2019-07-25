<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Selection extends Model
{
    protected $table = 'selections';
    public $primaryKey = 'id';
    public $timestamps = false;

    public function selectionBets()
    {
        return $this->hasMany('App\BetSelections');
    }
}