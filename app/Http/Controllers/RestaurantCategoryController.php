<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\tb_restaurant_category;
use App\tb_restaurant;

class RestaurantCategoryController extends Controller
{
  function create(Request $req){
    $restaurant_category = new tb_restaurant_category;
    $restaurant_category->name = $req->name;
    $restaurant_category->save();
    return response()->json(['success' => $restaurant_category]);
  }

  function readRestaurantCategory(){
    $restaurant = tb_restaurant_category::get();
    return response()->json(['data' => $restaurant]);
  }

  function readRestaurantByCategory(){
    $id_restaurant_category = request('id_restaurant_category');
    $restaurant = tb_restaurant::where('id_restaurant_category', '=', $id_restaurant_category)->get();
    return response()->json(['data' => $restaurant]);
  }

  function delete(tb_restaurant_category $restaurant_category){
    $id_restaurant_category = $restaurant_category->id;
    $restaurant_category::where('id', $id_restaurant_category)->delete();
    return response()->json(['success' => 'Restaurant Category has been deleted']);
  }
}
