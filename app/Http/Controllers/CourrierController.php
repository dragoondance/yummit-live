<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Courrier;

class CourrierController extends Controller
{
    function register(Request $req){
      $courrier = new Courrier;
      $courrier->name = $req->name;
      $courrier->phone_number = $req->phone_number;
      $courrier->status = $req->status;
      $courrier->save();

      return response()->json(['success' => $courrier]);
    }

    function readAllCourrier() {
      $courrier = Courrier::get();

      return response()->json(['data' => $courrier]);
    }

    function readCourrierById(Request $req) {
      $id_courrier = $req->id_courrier;
      $courrier = Courrier::where('id', '=', $id_courrier)->first();

      return response()->json(['success' => $courrier]);
    }

    function update(Request $req) {
      $id_courrier = $req->id_courrier;

      $update = [
        'name' => $req->name,
        'phone_number' => $req->phone_number,
        'status' => $req->status
      ];

      Courrier::where('id', '=', $id_courrier)->update($update);

      return response()->json(['success' => 'Courrier has been updated']);
    }
}
