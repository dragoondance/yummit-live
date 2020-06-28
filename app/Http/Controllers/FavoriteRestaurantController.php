<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use DB;
use App\tb_restaurant;
use App\tb_favorite_restaurant;

class FavoriteRestaurantController extends Controller
{
  function create(Request $req){
    $id_user = Auth::user()->id;
    $favorite_restaurant = new tb_favorite_restaurant;
    $favorite_restaurant->id_restaurant = $req->id_restaurant;
    $favorite_restaurant->id_user = $id_user;
    $favorite_restaurant->save();
    return response()->json($favorite_restaurant);
  }

  function readByUserId(){
    $id_user = Auth::user()->id;
    $favorite_restaurant = DB::table('tb_favorite_restaurants')
                      ->where('id_user', '=', $id_user)
                      ->leftJoin('tb_restaurants', 'tb_restaurants.id', '=', 'tb_favorite_restaurants.id_restaurant')
                      ->get();
    return response()->json(['data' => $favorite_restaurant]);
  }

  function deleteById(Request $req){
    $user = Auth::user();
    $restaurant_id = $req->id_restaurant;
    $deleted = tb_favorite_restaurant::where(['id_user' => $user->id, 'id_restaurant' => $restaurant_id])->delete();
    return response()->json(['success' => 'Favorite restaurant has been deleted']);
  }
}
