<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tb_favorite_food extends Model
{
  public $timestamps = true;

  protected $fillable = [
    'id_food'
  ];

  public function user(){
    return $this->belongsTo('App\User');
  }

  public function food(){
    return $this->belongsTo('App\tb_food');
  }
}
