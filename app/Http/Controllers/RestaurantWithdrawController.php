<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\tb_restaurant_withdraw;
use DB;
use Illuminate\Support\Facades\Auth;

class RestaurantWithdrawController extends Controller
{
    function create(Request $req){
      $restaurant = Auth::guard('restaurant-api')->user();

      $restaurant_balance = DB::table('tb_restaurants')
                            ->where('id', '=', $restaurant->id)
                            ->value('balance');

      if ($restaurant_balance >= $req->amount) {
        $withdraw = new tb_restaurant_withdraw;
        $withdraw->name_of_card = $req->name_of_card;
        $withdraw->name_of_bank = $req->name_of_bank;
        $withdraw->account_number = $req->account_number;
        $withdraw->amount = $req->amount;
        $withdraw->id_restaurant = $restaurant->id;
        $withdraw->save();

        return response()->json(['success' => $withdraw]);
      } else {
        return response()->json(['error' => 'insufficient balance']);
      }
    }

    function readAllByRestaurantId(){
      $restaurant = Auth::guard('restaurant-api')->user();
      $withdraw = DB::table('tb_restaurant_withdraws')
                  ->where('id_restaurant', '=', $restaurant->id)
                  ->get();
      return response()->json(['data' => $withdraw]);
    }

    function delete(Request $req){
      DB::table('tb_restaurant_withdraws')
      ->where('id', '=', $req->id_withdraw)
      ->delete();
      return response()->json(['success' => 'Withdraw has been canceled']);
    }
}
