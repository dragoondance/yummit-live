<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use DB;
use App\tb_order_food;

class OrderFoodController extends Controller
{
    function create(Request $req){
      $order_food = new tb_order_food;
      $order_food->quantity = $req->quantity;
      $order_food->note = $req->note;
      $order_food->id_order = $req->id_order;
      $order_food->id_food = $req->id_food;
      $order_food->save();
      return response()->json(['success' => $order_food]);
    }

    function readAllOrderFoodByOrderId(){
      $order_id = Request()->order_id;
      $order = DB::table('tb_order_foods')
              ->where('id_order', $order_id)
              ->join('tb_foods', 'tb_foods.id' ,'=', 'tb_order_foods.id_food')
              ->get();
      return response()->json(['data' => $order]);
    }


}
