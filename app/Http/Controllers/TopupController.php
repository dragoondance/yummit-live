<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use DB;
use App\tb_topup;
use App\tb_balance_history;
use Carbon\Carbon;
class TopupController extends Controller
{
  function create(Request $req){
    $id_user = Auth::user()->id;
    $unique_code = $req->unique_code;

    if (tb_topup::where('unique_code', '=', $unique_code)->exists()) {
      if (tb_topup::where('status', '=', 'UNPAID')->exists() || tb_topup::where('status', '=', 'PENDING')->exists()) {
        return response()->json(['success' => 'Unique code cannot be used']);
      } else {
        $topup = new tb_topup;
        $topup->balance = $req->balance;
        $topup->unique_code = $req->unique_code;
        $topup->id_user = $id_user;
        $topup->save();
        return response()->json(['success' => $topup]);
      }
    } else {
      $topup = new tb_topup;
      $topup->balance = $req->balance;
      $topup->unique_code = $req->unique_code;
      $topup->id_user = $id_user;
      $topup->save();
      return response()->json(['success' => $topup]);
    }
  }

  function readStatusUnpaidPending(){
    $id_user = Auth::user()->id;
    $topups = DB::table('tb_topups')
              ->where([
                ['id', '=', $id_user],
                ['status', '=', 'UNPAID' || 'status', '=', 'PENDING']
              ])
              ->get();
    return response()->json(['data' => $topups]);
  }

  function updateSlipImage(Request $req){
    $id_user = Auth::user()->id;
    $id_topup = $req->id_topup;

    $topupBalance = DB::table('tb_topups')
                    ->where('id', '=', $id_topup)
                    ->value('balance');

    $file = $req->file('slip_image');
    if ($file != null) {
      $fileName = $file->getClientOriginalName();
      $fileExtension = $file->getClientOriginalExtension();
      $finalFileName = $fileName . '_' . time() . '.' . $fileExtension;
      $file->move('slip_image', $finalFileName);

      $balance_history = new tb_balance_history;
      $balance_history->description = 'Topup';
      $balance_history->balance = $topupBalance;
      $balance_history->id_user = $id_user;
      $balance_history->save();

      DB::table('tb_topups')
          ->where([
                    ['id_user', '=', $id_user],
                    ['id', '=', $id_topup]
                  ])
          ->update([
                    'status' => 'PENDING',
                    'slip_image' => url('slip_image/' . $finalFileName),
                    'id_balance_history' => $balance_history->id
                  ]);
      return response()->json(['message' => 'Topup updated']);
    } else {
      return response()->json(['message' => 'Image does not exist']);
    }
  }
}
