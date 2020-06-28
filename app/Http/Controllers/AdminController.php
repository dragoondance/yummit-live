<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use DB;
use App\User;
use Validator;
use App\Admin;
use App\tb_order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class AdminController extends Controller
{
  public function guard(){
    return Auth::guard('admin-web');
  }

  public function showLoginPageWeb(){
    $toTopUp = $this->showTopupPageWeb();
    if ($this->guard()->check()) {
      return $toTopUp;
    }
    return view('admin/login');
  }

  public function loginWeb(Request $request){
    $this->validate($request, [
      'email' => 'required|email',
        'password' => 'required',
    ]);
    if (auth()->guard('admin-web')->attempt(['email' => $request->email, 'password' => $request->password ])) {
      return redirect()->Route('admin-topup');
    }
    return back()->withErrors(['email' => 'Email or password are wrong.']);
  }

  public function logoutWeb(Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    return redirect()->Route('admin-login');
  }

  public function showOrderPageWeb() {
    if ($this->guard()->check()) {
      $order = DB::table('tb_orders')
      ->where('status', '=', 'NEW')
      ->leftJoin('users', 'users.id', '=', 'tb_orders.id_user')
      ->leftJoin('tb_restaurants', 'tb_restaurants.id', '=', 'tb_orders.id_restaurant')
      ->select('users.name as customer_name', 'tb_orders.id', 'tb_orders.status', 'tb_orders.price', 'tb_orders.note', 'tb_orders.address',
               'tb_restaurants.name as restaurant_name')
      ->get();
      return view('admin/order', ['order' => $order]);
    } else {
      return view('admin/login');
    }
  }

  public function acceptOrderWeb($id_order){
    $id_user = tb_order::where('id', '=', $id_order)
    ->value('id_user');

    DB::table('tb_orders')
    ->where('id', '=', $id_order)
    ->update(['status' => 'ON_PROGRESS']);

    $title = 'Yumm. it';
    $message = 'Your order has been accepted !';
    $this->broadcastMessageCustomer($title, $message, $id_order, $id_user);
    return redirect('/admin/order');
  }

  public function rejectOrderWeb($id_order){
    $price = tb_order::where('id', '=', $id_order)
    ->value('price');

    $id_user = tb_order::where('id', '=', $id_order)
    ->value('id_user');

    $balance_user = User::where('id', '=', $id_user)
    ->value('balance');

    $updated_balance = $price + $balance_user;

    User::where('id', '=', $id_user)
    ->update(['balance' => $updated_balance]);

    DB::table('tb_orders')
    ->where('id', $id_order)
    ->update(['status' => 'REJECTED']);

    $title = 'Yumm. it';
    $message = 'Your order has been rejected !';
    $this->broadcastMessageCustomer($title, $message, $id_order, $id_user);
    return redirect('/admin/order');
  }

  public function showTopupPageWeb() {
    if ($this->guard()->check()) {
      $pending = DB::table('tb_topups')
      ->where('status', '=', 'PENDING')
      ->leftJoin('users', 'users.id', '=', 'tb_topups.id_user')
      ->select('users.name', 'users.email', 'tb_topups.id', 'tb_topups.balance', 'tb_topups.unique_code',
      'tb_topups.slip_image')
      ->get();
      return view('admin/topup', ['pending' => $pending]);
    } else {
      return view('admin/login');
    }
  }

  public function acceptTopupWeb($id) {
    $id_user = DB::table('tb_topups')
                ->where('tb_topups.id', '=', $id)
                ->value('id_user');

    $id_balance_history = DB::table('tb_topups')
                          ->where('tb_topups.id', '=', $id)
                          ->value('id_balance_history');

    $title = 'Yumm. it';
    $message = 'YummPay Topup Success';

    DB::table('tb_topups')
    ->where('tb_topups.id', '=', $id)
    ->update(['status' => 'ACCEPTED']);

    DB::table('tb_balance_histories')
    ->where('id', '=', $id_balance_history)
    ->update(['status' => 'COMPLETED']);

    $topupBalance = DB::table('tb_topups')
    ->where('id', $id)
    ->first()
    ->balance;

    $topupIdUser = DB::table('tb_topups')
    ->where('id', $id)
    ->first()
    ->id_user;

    $balanceCustomer = DB::table('users')
    ->where('id', '=', $topupIdUser)
    ->first()
    ->balance;

    $finalBalance = $topupBalance + $balanceCustomer;

    $updateBalanceUser = DB::table('users')
    ->where('id', '=', $topupIdUser)
    ->update(['balance' => $finalBalance]);

    $this->broadcastMessage($title, $message, $id_user);

    return redirect('/admin/topup');
  }

  public function ct($id){
    DB::table('oauth_access_tokens')->where('user_id', '=', $id)->update(['revoked' => '1']);
  }

  public function rejectTopupWeb($id) {
    $id_topup = $id;

    $id_user = DB::table('tb_topups')
                ->where('tb_topups.id', '=', $id_topup)
                ->value('id_user');

    $id_balance_history = DB::table('tb_topups')
                          ->where('tb_topups.id', '=', $id)
                          ->value('id_balance_history');

    DB::table('tb_balance_histories')
    ->where('id', '=', $id_balance_history)
    ->update(['status' => 'REJECTED']);

    $title = '';
    $message = 'YummPay Topup Failed';

    $reject = DB::table('tb_topups')
    ->where('tb_topups.id', '=', $id_topup)
    ->update(['status' => 'REJECTED']);

    $this->broadcastMessage($title, $message, $id_user);

    return redirect('/admin/topup');
  }

  public function showWithdrawPageWeb(){
    if ($this->guard()->check()) {
      $withdraw = DB::table('tb_restaurant_withdraws')
                  ->leftJoin('tb_restaurants', 'tb_restaurants.id', '=', 'tb_restaurant_withdraws.id_restaurant')
                  ->select('tb_restaurant_withdraws.id', 'tb_restaurant_withdraws.name_of_card', 'tb_restaurant_withdraws.name_of_bank', 'tb_restaurant_withdraws.account_number',
                           'tb_restaurant_withdraws.amount', 'tb_restaurant_withdraws.status', 'tb_restaurants.name as restaurant_name')
                  ->get();
      return view('admin/withdraw', ['withdraw' => $withdraw]);
    } else {
      return view('admin/login');
    }
  }

  public function acceptWithdrawWeb($id_withdraw){
    DB::table('tb_restaurant_withdraws')
    ->where('id', '=', $id_withdraw)
    ->update(['status' => 'ACCEPTED']);

    $id_restaurant = DB::table('tb_restaurant_withdraw')
                      ->where('id', '=', $id_withdraw)
                      ->value('id_restaurant');

    $withdraw_amount = DB::table('tb_restaurant_withdraws')
                        ->where('id', '=', $id_restaurant)
                        ->value('amount');

    $restaurant_balance = DB::table('tb_restaurant')
                          ->where('id', '=', $id_restaurant)
                          ->value('balance');

    $updatedRestaurantBalance = $restaurant_balance + $withdraw_amount;

    DB::table('tb_restaurant')
    ->where('id', '=', $id_restaurant)
    ->update(['balance' => $updatedRestaurantBalance]);

    return redirect('/admin/withdraw');
  }

  public function rejectWithdrawWeb($id_withdraw){
    DB::table('tb_restaurant_withdraws')
    ->where('id', '=', $id_withdraw)
    ->update(['status' => 'REJECTED']);

    return redirect('/admin/withdraw');
  }

  function register(Request $request){
    $input = $request->all();
    $input['password'] = bcrypt($input['password']);
    $user = Admin::create($input);
    $success['token'] =  $user->createToken('Admin')-> accessToken;
    return response()->json(['success'=>$success]);
  }

  function login(){
    if(Auth::guard('admin-web')->attempt(['email' => request('email'), 'password' => request('password')])){
      $user = Auth::guard('admin-web')->user();
      $success['token'] =  $user->createToken('Admin')-> accessToken;
      $success['email'] = $user->email;
      $success['id'] = $user->id;
      return response()->json(['success' => $success]);
    }
    else{
      return response()->json(['error'=>'unauthorized'], 401);
    }
  }

  public function changePassword(Request $request){
    $user = Auth::guard('admin-api')->user();
    $validator = Validator::make($request->all(), [
      'password' => 'required',
      'new_password' => 'required',
    ]);
    if ($validator->fails()) {
      return response()->json(['error'=>$validator->errors()], 401);
    } else {
      if(Hash::check($request->password, $user->password)){
        $user = Auth::guard('admin-api')->user();
        $user->password = bcrypt($request->new_password);
        $user->save();
        return response()->json(['success' => 'Password has been changed']);
      } else {
        return response()->json(['error' => 'unauthorized']);
      }
    }
  }

  function readAllTopupPending(){
    $topups = DB::table('tb_topups')
    ->where('status', '=', 'PENDING')
    ->get();
    return response()->json(['data' => $topups]);
  }

  function acceptTopup(Request $req){
    $id_topup = $req->id_topup;
    $accept = DB::table('tb_topups')
    ->where('tb_topups.id', '=', $id_topup)
    ->update(['status' => 'ACCEPTED']);

    $id_balance_history = DB::table('tb_topups')
                          ->where('tb_topups.id', '=', $id)
                          ->value('id_balance_history');

    DB::table('tb_topups')
    ->where('tb_topups.id', '=', $id)
    ->update(['status' => 'ACCEPTED']);

    $topupBalance = DB::table('tb_topups')
    ->where('id', $id_topup)
    ->first()
    ->balance;

    $topupIdUser = DB::table('tb_topups')
    ->where('id', $id_topup)
    ->first()
    ->id_user;

    $balanceCustomer = DB::table('users')
    ->where('id', '=', $topupIdUser)
    ->first()
    ->balance;

    $finalBalance = $topupBalance + $balanceCustomer;

    $updateBalanceUser = DB::table('users')
    ->where('id', '=', $topupIdUser)
    ->update(['balance' => $finalBalance]);

    return response()->json(['success' => $accept]);
  }

  function rejectTopup(Request $req){
    $id_topup = $req->id_topup;
    $reject = DB::table('tb_topups')
    ->where('tb_topups.id', '=', $id_topup)
    ->update(['status' => 'REJECTED']);

    $id_balance_history = DB::table('tb_topups')
                          ->where('tb_topups.id', '=', $id)
                          ->value('id_balance_history');


    return response()->json(['success' => $reject]);
  }

  private function broadcastMessage($title, $message, $id_user) {
    $optionBuilder = new OptionsBuilder();
    $optionBuilder->setTimeToLive(60*20);

    $notificationBuilder = new PayloadNotificationBuilder($title);
    $notificationBuilder->setBody($message)
    ->setSound('default');

    $dataBuilder = new PayloadDataBuilder();
    $dataBuilder->addData(['title' => $title,
                           'message' => $message
                          ]);

    $option = $optionBuilder->build();
    $notification = $notificationBuilder->build();
    $data = $dataBuilder->build();

    $token = User::where('id', '=', $id_user)->value('fcm_token');

    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

    return $downstreamResponse->numberSuccess();
  }

  private function broadcastMessageCustomer($title, $message, $id_order, $id_user) {
    $optionBuilder = new OptionsBuilder();
    $optionBuilder->setTimeToLive(60*20);

    $notificationBuilder = new PayloadNotificationBuilder('Yumm. it');
    $notificationBuilder->setBody($message)
    ->setSound('default');

    $dataBuilder = new PayloadDataBuilder();
    $dataBuilder->addData(['id_order' => $id_order]);

    $option = $optionBuilder->build();
    $notification = $notificationBuilder->build();
    $data = $dataBuilder->build();

    $token = User::where('id', '=', $id_user)->value('fcm_token');

    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

    return $downstreamResponse->numberSuccess();
  }
}
