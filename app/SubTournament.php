<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubTournament extends Model
{
    public $primaryKey = 'id';
    public $timestamps = false;
    protected $table = 'sub_tournaments';

    public function tournament()
    {
        return $this->belongsTo('App\Tournament', 'tournament_id');
    }
}
