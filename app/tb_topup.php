<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tb_topup extends Model
{
  public $timestamps = true;

  protected $fillable = [
    'real_balance', 'unique_balance', 'slip_image', 'id_user'
  ];

  public function user(){
    return $this->belongsTo('App\User');
  }

  public function balance_history(){
    return $this->belongsTo('App\tb_balance_history');
  }
}
