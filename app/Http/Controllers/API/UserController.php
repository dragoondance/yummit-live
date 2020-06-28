<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Illuminate\Support\Facades\Hash;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class UserController extends Controller
{
  public $successStatus = 200;
  /**
  * login api
  *
  * @return \Illuminate\Http\Response
  */
  public function login() {
    if(Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
      $user = Auth::user();
      $success['token'] =  $user->createToken('User')-> accessToken;
      $success['name'] = $user->name;
      $success['balance'] = $user->balance;
      $success['phone_number'] = $user->phone_number;
      $success['address'] = $user->address;
      $success['id'] = $user->id;

      DB::table('users')
      ->where('email', '=', request('email'))
      ->update(['fcm_token' => request('fcm_token')]);

      return response()->json(['success' => $success], $this-> successStatus);
    } else {
      return response()->json(['error'=>'UNAUTHORISED']);
    }
  }
  /**
  * Register api
  *
  * @return \Illuminate\Http\Response
  */
  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required|email',
      'password' => 'required',
      'c_password' => 'required|same:password',
    ]);
    if ($validator->fails()) {
      return response()->json(['error'=>$validator->errors()], 401);
    }
    $input = $request->all();
    $input['password'] = bcrypt($input['password']);
    $user = User::create($input);
    $success['token'] =  $user->createToken('MyApp')-> accessToken;
    $success['name'] =  $user->name;
    $success['email'] = $user->email;
    return response()->json(['success'=>$success], $this-> successStatus);
  }
  /**
  * details api
  *
  * @return \Illuminate\Http\Response
  */
  public function details()
  {
    $user = Auth::user();
    return response()->json(['success' => $user], $this-> successStatus);
  }

  public function changePassword(Request $request){
    $validator = Validator::make($request->all(), [
      'password' => 'required',
      'new_password' => 'required',
      'c_new_password' => 'required|same:new_password',
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => 'UNAUTHORISED']);
    } else {
      if(Hash::check($request->password, Auth::user()->password)){
        $user = Auth::user();
        $user->password = bcrypt($request->new_password);
        $user->save();
        return response()->json(['success' => 'PASSWORD HAS BEEN CHANGED']);
      } else {
        return response()->json(['error' => 'WRONG PASSWORD']);
      }
    }
  }

  function update(Request $req) {
    $user = Auth::user();
    $update = DB::table('users')
              ->where('id', $user->id)
              ->update(['name' => $req->name,
                        'email' => $req->email,
                        'phone_number' => $req->phone_number,
                        'address' => $req->address
                      ]);
    return response()->json(['success' => $update]);
  }

  function updateBalance(Request $req){
    $user = Auth::user();
    $balance = $req->balance;
    $update = DB::table('users')
              ->where('id', $user->id)
              ->update(['balance' => $balance]);
    return response()->json(['updated_balance' => $balance]);
  }

  public function logout(Request $request){
    // unauthorized token
    $request->user()->token()->revoke();

    return response()->json([
      'message' => 'Successfully logged out'
    ]);
  }
}
