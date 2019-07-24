<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BalanceTransaction extends Model
{
    protected $table = 'balance_transactions';
    public $primaryKey = 'id';

    public function user(){
        return $this->belongsTo('App\User');
    }
}
