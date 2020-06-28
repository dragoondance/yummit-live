<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use DB;
use App\tb_food;
use App\tb_restaurant;
use App\tb_favorite_food;

class FavoriteFoodController extends Controller
{
  function create(Request $req){
    $id_user = Auth::user()->id;
    $favorite_food = new tb_favorite_food;
    $favorite_food->id_food = $req->id_food;
    $favorite_food->id_user = $id_user;
    $favorite_food->save();
    return response()->json($favorite_food);
  }

  function readByUserId(){
    $id_user = Auth::user()->id;
    $favorite_foods = DB::table('tb_favorite_foods')
    ->where('id_user', '=', $id_user)
    ->leftJoin('tb_foods', 'tb_foods.id', '=', 'tb_favorite_foods.id_food')
    ->get();

    $data = [];

    foreach ($favorite_foods as $favorite_food) {
      $restaurant = tb_restaurant::where('id', '=', $favorite_food->id_restaurant)
      ->select('id', 'name', 'phone_number', 'restaurant_image', 'restaurant_header', 'address', 'open_time', 'close_time',
      'description', 'rating', 'latitude', 'longitude')
      ->first();

      $data[] = ['id' => $favorite_food->id,
                 'id_food' => $favorite_food->id_food,
                 'id_user' => $favorite_food->id_user,
                 'created_at' => $favorite_food->created_at,
                 'name' => $favorite_food->name,
                 'price' => $favorite_food->price,
                 'description' => $favorite_food->description,
                 'food_image' => $favorite_food->food_image,
                 'is_available' => $favorite_food->is_available,
                 'id_food_category' => $favorite_food->id_food_category,
                 'restaurant' => $restaurant
    ];
  }
  return response()->json(['data' => $data]);
}

function deleteById(Request $req){
  $user = Auth::user();
  $food_id = $req->id_food;
  $deleted = tb_favorite_food::where(['id_user' => $user->id, 'id_food' => $food_id])->delete();
  return response()->json(['success' => 'Favorite food has been deleted']);
}
}
