<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\tb_sales_history;
use App\tb_restaurant;

class SalesHistoryController extends Controller
{
  function create(Request $req){
    $id_user = Auth::user()->id;
    $sales_history = new tb_balance_history;
    $sales_history->time = $req->time;
    $sales_history->description = $req->description;
    $sales_history->balance = $req->balance;
    $sales_history->id_user = $id_user;
    $sales_history->save();
    return response()->json(['success' => $sales_history]);
  }

  function readByUserId(){
    $id_user = Auth::user()->id;
    $balance_history = tb_balance_history::where('id_user', $id_user)
                        ->orderBy('id', 'asc')
                        ->get();
    return response()->json(['data' => $balance_history]);
  }
}
