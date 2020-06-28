<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use DB;
use App\tb_food;
use App\tb_restaurant;
use App\tb_favorite_food;
use App\tb_order_food;
use App\tb_discount;

class FoodController extends Controller
{
  function create(Request $req){
    $user = Auth::guard('restaurant-api')->user();
    $food = new tb_food;
    $food->name = $req->name;
    $food->price = $req->price;
    $food->description = $req->description;
    $food->is_available = $req->is_available;
    $file = $req->file('food_image');
    if ($file != null) {
      $fileName = $file->getClientOriginalName();
      $fileExtension = $file->getClientOriginalExtension();
      $finalFileName = $fileName . '_' . time() . '.' . $fileExtension;
      $file->move('food_image', $finalFileName);
      $food->food_image = url('food_image/' . $finalFileName);
    }
    $food->id_food_category = $req->id_food_category;
    $food->id_discount = $req->id_discount;
    $food->id_restaurant = $user->id;
    $food->save();
    return response()->json(['success' => $food]);
  }

  function update(Request $req){
    $user = Auth::guard('restaurant-api')->user();
    $id_food = $req->id_food;

    $file = $req->file('food_image');
    if ($file != null) {
      $fileName = $file->getClientOriginalName();
      $fileExtension = $file->getClientOriginalExtension();
      $finalFileName = $fileName . '_' . time() . '.' . $fileExtension;
      $file->move('food_image', $finalFileName);

      $updateDetails = ['name' => $req->name,
      'price' => $req->price,
      'description' => $req->description,
      'food_image' => url('food_image/' . $finalFileName),
      'is_available' => $req->is_available,
      'id_discount' => $req->id_discount];
    } else {
      $updateDetails = ['name' => $req->name,
      'price' => $req->price,
      'description' => $req->description,
      'is_available' => $req->is_available,
      'id_discount' => $req->id_discount];
    }
    $update = DB::table('tb_foods')
    ->where('id', '=', $id_food)
    ->update($updateDetails);
    return response()->json(['accept' => $updateDetails]);
  }

  // function customerReadByRestaurantId(){
  //   $user = Auth::user();
  //   $idRes = Request()->id_restaurant;
  //   $foods = DB::table('tb_foods')
  //             ->where('tb_foods.id_restaurant', '=', $idRes)
  //             ->leftJoin('tb_discounts', 'tb_discounts.id_food', '=', 'tb_foods.id')
              // ->leftJoin('tb_favorite_foods', function ($join) {
              //   $join->on('tb_favorite_foods.id_food', '=', 'tb_foods.id');
              //   $join->where('tb_favorite_foods.id_user', '=', Auth::user()->id);
              // })
  //             ->select('tb_foods.id', 'tb_foods.name', 'tb_foods.price', 'tb_foods.description', 'tb_foods.food_image',
  //                      'tb_foods.is_available', 'tb_foods.id_food_category', 'tb_foods.id_restaurant', 'tb_discounts.discount',
  //                      'tb_discounts.is_active', 'tb_discounts.start_time', 'tb_discounts.end_time',
  //                      'tb_favorite_foods.id_food')
  //             ->get();
  //   return response()->json(['data' => $foods]);
  // }

  function customerReadByRestaurantId() {
    $user = Auth::user();
    $idRes = Request()->id_restaurant;
    $foods = DB::table('tb_foods')
    ->where('tb_foods.id_restaurant', '=', $idRes)
    ->leftJoin('tb_favorite_foods', function ($join) {
      $join->on('tb_favorite_foods.id_food', '=', 'tb_foods.id');
      $join->where('tb_favorite_foods.id_user', '=', Auth::user()->id);
    })
    ->select('tb_foods.id', 'tb_foods.name', 'tb_foods.price', 'tb_foods.description', 'tb_foods.food_image',
             'tb_foods.is_available', 'tb_foods.id_food_category', 'tb_foods.id_discount', 'tb_foods.id_restaurant',
             'tb_foods.created_at', 'tb_favorite_foods.id_food as is_favorite', 'tb_favorite_foods.id_user')
    ->get();

    $data = [];
    foreach ($foods as $food) {
      $discount = tb_discount::where('id', '=', $food->id_discount)
      ->first();

      if ($food->is_favorite == null) {
        $food->is_favorite = 'FALSE';
      } else {
        $food->is_favorite = 'TRUE';
      }

      $data[] = ['id' => $food->id,
      'name' => $food->name,
      'price' => $food->price,
      'description' => $food->description,
      'food_image' => $food->food_image,
      'is_available' => $food->is_available,
      'id_food_category' => $food->id_food_category,
      'id_discount' => $food->id_discount,
      'id_restaurant'  => $food->id_restaurant,
      'created_at' => $food->created_at,
      'is_favorite' => $food->is_favorite,
      'discount' => $discount
    ];
  }

  return response()->json(['data' => $data]);
}

function restaurantReadByRestaurantId() {
  $idRes = Auth::guard('restaurant-api')->user()->id;
  $foods = DB::table('tb_foods')
  ->where('id_restaurant', '=', $idRes)
  ->get();

  $data = [];
  foreach ($foods as $food) {
    $discount = tb_discount::where('id', '=', $food->id_discount)
    ->first();

    $data[] = ['id' => $food->id,
    'name' => $food->name,
    'price' => $food->price,
    'description' => $food->description,
    'food_image' => $food->food_image,
    'is_available' => $food->is_available,
    'id_food_category' => $food->id_food_category,
    'id_restaurant'  => $food->id_restaurant,
    'created_at' => $food->created_at,
    'discount' => $discount
  ];
}
  return response()->json(['data' => $data]);
}

function delete(){
  $idFood = Request()->id_food;
  tb_favorite_food::where('id_food', $idFood)->delete();
  tb_order_food::where('id_food', $idFood)->delete();
  tb_food::where('id', $idFood)->delete();
  return response()->json(['deleted' => 'Food has been deleted']);
}
}
