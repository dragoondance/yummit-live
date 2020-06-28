<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

use App\tb_restaurant;
use App\tb_discount;

class DiscountController extends Controller
{
  function create(Request $req){
    $user = Auth::guard('restaurant-api')->user();

    $discount = new tb_discount;
    $discount->name = $req->name;
    $discount->discount = $req->discount;
    $discount->is_active = $req->is_active;
    $discount->start_time = $req->start_time;
    $discount->end_time = $req->end_time;
    $discount->id_restaurant = $user->id;
    $discount->save();

    return response()->json(['success' => $discount]);
  }

  function readDiscountByFoodId(Request $req){
    $id_food = $req->id_food;
    $discount = tb_discount::where('id_food', $id_food)
               ->get();
    return response()->json(['data' => $discount]);
  }

  function readDiscountByRestaurantId(Request $req){
    $user = Auth::guard('restaurant-api')->user();
    $discount = tb_discount::where('id_restaurant', $user->id)
               ->get();
    return response()->json(['data' => $discount]);
  }

  function editDiscountByDiscountId(Request $req){
    $user = Auth::guard('restaurant-api')->user();
    $id_discount = $req->id_discount;
    $updateDetails = ['is_active' => $req->is_active,
                      'start_time' => $req->start_time,
                      'end_time' => $req->end_time,
                      'discount' => $req->discount];

    $accept = DB::table('tb_discounts')
              ->where('id', '=', $id_discount)
              ->update($updateDetails);
    return response()->json(['success' => $updateDetails]);
  }
}
