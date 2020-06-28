<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tb_food_category extends Model
{
  public $timestamps = true;

  protected $fillable = [
    'name'
  ];

  public function foods(){
    return $this->hasMany('App\tb_food');
  }
}
