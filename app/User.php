<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
  use HasApiTokens, Notifiable;

  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'name', 'email', 'password', 'phone_number', 'balance', 'fcm_token', 'address'
  ];

  /**
  * The attributes that should be hidden for arrays.
  *
  * @var array
  */
  protected $hidden = [
    'password', 'remember_token',
  ];

  /**
  * The attributes that should be cast to native types.
  *
  * @var array
  */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function orders(){
    return $this->hasMany('App\tb_order');
  }

  public function balance_histories(){
    return $this->hasMany('App\tb_balance_history');
  }

  public function topups(){
    return $this->hasMany('App\tb_topup');
  }

  public function vouchers(){
    return $this->hasMany('App\tb_voucher');
  }

  public function favorite_foods(){
    return $this->hasMany('App\tb_favorite_food');
  }

  public function favorite_restaurants(){
    return $this->hasMany('App\tb_favorite_restaurant');
  }
}
