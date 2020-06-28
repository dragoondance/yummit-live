<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Courrier extends Model
{

  public $timestamps = false;

  protected $fillable = [
    'name', 'phone_number', 'status'
  ];

  public function orders(){
    return $this->hasMany('App\tb_order');
  }
}
