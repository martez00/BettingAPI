<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $primaryKey = 'id';
    public $timestamps = false;
    protected $table = 'countries';

    public function tournaments()
    {
        return $this->hasMany('App\Tournament', 'country_id');
    }
}
