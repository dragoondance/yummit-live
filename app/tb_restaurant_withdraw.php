<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tb_restaurant_withdraw extends Model
{
  public $timestamps = true;

  protected $fillable = [
    'name_of_card', 'name_of_bank', 'account_number', 'amount', 'status'
  ];

  public function restaurant(){
    return $this->belongsTo('App\tb_restaurant');
  }
}
