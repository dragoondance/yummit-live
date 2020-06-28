<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\tb_balance_history;
use App\UserController;

class BalanceHistoryController extends Controller
{
  function create(Request $req){
    $id_user = Auth::user()->id;
    $balance_history = new tb_balance_history;
    $balance_history->date = $req->date;
    $balance_history->description = $req->description;
    $balance_history->balance = $req->balance;
    $balance_history->id_user = $id_user;
    $balance_history->save();
    return response()->json(['success' => $balance_history]);
  }

  function readByUserId(){
    $id_user = Auth::user()->id;
    $balance_history = tb_balance_history::where('id_user', $id_user)
                        ->orderBy('id', 'asc')
                        ->get();
    return response()->json(['data' => $balance_history]);
  }
}
