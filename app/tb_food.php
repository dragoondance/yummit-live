<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tb_food extends Model
{
  public $timestamps = true;

  protected $fillable = [
    'name', 'price', 'description', 'food_image', 'is_available', 'id_foodCategory'
  ];

  public function foodCategory(){
    return $this->belongsTo('App\tb_foodCategory');
  }

  public function restaurant(){
    return $this->belongsTo('App\tb_restaurant');
  }

  public function orders(){
    return $this->hasMany('App\order');
  }

  public function orderFood(){
    return $this->belongsTo('App\tb_orderFood');
  }
}
