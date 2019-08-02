<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    public $primaryKey = 'id';
    public $timestamps = false;
    protected $table = 'sports';

    public function tournaments()
    {
        return $this->hasMany('App\Tournament', 'sport_id');
    }
}
