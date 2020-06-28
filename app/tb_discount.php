<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tb_discount extends Model
{
  public $timestamps = true;

  protected $fillable = [
    'name', 'discount', 'is_active', 'start_time', 'end_time' , 'id_food', 'id_restaurant'
  ];

  public function food(){
    return $this->belongsTo('App\tb_food');
  }

  public function restaurant(){
    return $this->belongsTo('App\tb_restaurant');
  }
}
