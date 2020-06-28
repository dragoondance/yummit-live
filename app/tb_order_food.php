<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tb_order_food extends Model
{
  public $timestamps = true;

  protected $fillable = [
    'quantity', 'note', 'id_order', 'id_food'
  ];

  public function order(){
    return $this->belongsTo('App\tb_order');
  }

  public function food(){
    return $this->belongsTo('App\tb_food');
  }
}
