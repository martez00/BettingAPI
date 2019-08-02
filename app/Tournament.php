<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    public $primaryKey = 'id';
    public $timestamps = false;
    protected $table = 'tournaments';

    public function subTournaments()
    {
        return $this->hasMany('App\SubTournament', 'tournament_id');
    }

    public function sport()
    {
        return $this->belongsTo('App\Sport', 'sport_id');
    }

    public function country()
    {
        return $this->belongsTo('App\Country', 'country_id');
    }
}
