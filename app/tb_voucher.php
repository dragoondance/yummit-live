<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tb_voucher extends Model
{
  public $timestamps = true;

  protected $fillable = [
    'name', 'discount', 'max_discount', 'is_used', 'id_user'
  ];

  public function user(){
    return $this->belongsTo('App\User');
  }
}
