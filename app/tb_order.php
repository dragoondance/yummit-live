<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tb_order extends Model
{
  public $timestamps = true;

  protected $fillable = [
    'status', 'price', 'note', 'delivery_fee', 'pickup_time', 'order_type', 'address', 'longitude', 'latitude', 'id_user',
    'id_restaurant', 'id_voucher', 'id_balance_history', 'id_courrier'
  ];

  public function user(){
    return $this->belongsTo('App\User');
  }

  public function restaurant(){
    return $this->belongsTo('App\tb_restaurant');
  }

  public function orderFoods(){
    return $this->hasMany('App\tb_orderFood');
  }

  public function voucher(){
    return $this->belongsTo('App\tb_voucher');
  }

  public function balance_history(){
    return $this->belongsTo('App\tb_balance_history');
  }

  public function courrier(){
    return $this->belongsTo('App\Courrier');
  }
}
