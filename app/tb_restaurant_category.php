<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tb_restaurant_category extends Model
{
  public $timestamps = true;

  protected $fillable = [
    'name'
  ];

  public function restaurant(){
    return $this->belongsTo('App\tb_restaurant');
  }
}
