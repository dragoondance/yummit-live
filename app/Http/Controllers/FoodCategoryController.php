<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\tb_food_category;

class FoodCategoryController extends Controller
{
  function create(Request $req){
    $food_category = new tb_food_category;
    $food_category->name = $req->name;
    $food_category->save();
    return response()->json(['success' => $food_category]);
  }

  function delete(tb_food_category $food_category){
    $id_food_category = $food_category->id;
    $food_category::where('id', $id_food_category)->delete();
    return response()->json(['deleted' => 'Food Category has been deleted']);
  }
}
