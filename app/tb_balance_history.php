<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tb_balance_history extends Model
{
  public $timestamps = true;

  protected $fillable = [
    'date', 'description', 'balance', 'id_user'
  ];

  public function user(){
    return $this->belongsTo('App\User');
  }

  public function order(){
    return $this->belongsTo('App\tb_order');
  }
}
