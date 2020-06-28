<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class tb_restaurant extends Authenticatable
{
  use HasApiTokens, Notifiable;

  protected $table = 'tb_restaurants';
  protected $guard = 'restaurant-api';

  public $timestamps = true;

  protected $fillable = [
    'name', 'email', 'password', 'phone_number', 'restaurant_image', 'restaurant_header', 'address', 'open_time', 'close_time',
    'description', 'rating', 'balance', 'fcm_token', 'latitude', 'longitude', 'id_restaurant_category'
  ];

  public function foods(){
    return $this->hasMany('App\tb_food');
  }

  public function orders(){
    return $this->hasMany('App\tb_order');
  }

  public function discounts(){
    return $this->hasMany('App\tb_discount');
  }

  public function restaurantCategory(){
    return $this->belongsTo('App\tb_restaurant_category');
  }

  public function restaurantWithdraws(){
    return $this->hasMany('App\tb_restaurant_withdraw');
  }
}
