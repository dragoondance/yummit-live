<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\tb_voucher;

class VoucherController extends Controller
{
  function create(Request $req){
    $voucher = new tb_restaurant;
    $voucher->name = $req->name;
    $voucher->email = $req->email;
    $voucher->password = $req->password;
    $voucher->address = $req->address;
    $voucher->description = $req->description;
    $voucher->rating = $req->rating;
    $voucher->balance = $req->balance;
    $voucher->longitude = $req->longitude;
    $voucher->latitude = $req->latitude;
    $voucher->save();
    return response()->json(['success' => $voucher]);
  }

  function readAll(){
    return tb_voucher::all();
  }
}
