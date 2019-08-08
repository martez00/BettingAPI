<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    public $primaryKey = 'id';
    public $timestamps = false;
    protected $table = 'sports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description'
    ];

    public function tournaments()
    {
        return $this->hasMany('App\Tournament', 'sport_id');
    }
}
