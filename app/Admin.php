<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Admin extends Authenticatable
{
  use HasApiTokens, Notifiable;

  protected $table = 'admins';
  protected $guard = 'admin-api';

  public $timestamps = true;

  protected $fillable = [
    'email', 'password'
  ];
}
