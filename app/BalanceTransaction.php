<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BalanceTransaction extends Model
{
    public $primaryKey = 'id';
    public $timestamps = false;
    protected $table = 'balance_transactions';

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
