<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

use DB;
use App\User;
use App\tb_restaurant;
use App\tb_order_food;
use App\tb_balance_history;
use App\tb_order;
use App\tb_food;
use App\UserController;

class OrderController extends Controller
{
  function create(Request $req){
    $id_user = Auth::user()->id;
    $balance_user = Auth::user()->balance;

    $updated_balance = $balance_user - $req->price;

    $name_restaurant = tb_restaurant::where('id', '=', $req->id_restaurant)
    ->value('name');

    if ($updated_balance >= 0) {
      $balance_history = new tb_balance_history;
      $balance_history->description = $name_restaurant;
      $balance_history->balance = $req->price;
      $balance_history->id_user = $id_user;
      $balance_history->save();

      $order = new tb_order;
      $order->price = $req->price;
      $order->note = $req->note;
      $order->delivery_fee = $req->delivery_fee;
      $order->pickup_time = $req->pickup_time;
      $order->order_type = $req->order_type;
      $order->address = $req->address;
      $order->longitude = $req->longitude;
      $order->latitude = $req->latitude;
      $order->id_user = $id_user;
      $order->id_restaurant = $req->id_restaurant;
      $order->id_voucher = $req->id_voucher;
      $order->id_balance_history = $balance_history['id'];
      $order->save();

      User::where('id', '=', $id_user)
      ->update(['balance' => $updated_balance]);

      $title = 'Yumm. it';
      $message = 'New order !';

      $this->broadcastMessageRestaurant($title, $message, $order['id'], $req->id_restaurant);

      return response()->json(['success' => $order, 'balance_history' => $balance_history]);
    } else {
      return response()->json(['failed' => 'Your balance is not enough']);
    }
  }

  function readAllOrderByUserId(){
    $id_user = Auth::user()->id;

    $orders = DB::table('tb_orders')
    ->where('id_user', '=', $id_user)
    ->get();

    $data = [];

    foreach ($orders as $order) {
      $restaurant = tb_restaurant::where('id', '=', $order->id_restaurant)
                    ->select('id', 'name', 'phone_number', 'restaurant_image', 'restaurant_header', 'address', 'open_time', 'close_time',
                              'description', 'rating', 'latitude', 'longitude')
                    ->first();

      $data[] = ['id' => $order->id,
               'status' => $order->status,
               'price' => $order->price,
               'note' => $order->note,
               'delivery_fee' => $order->delivery_fee,
               'pickup_time' => $order->pickup_time,
               'order_type' => $order->order_type,
               'address' => $order->address,
               'created_at' => $order->created_at,
               'restaurant' => $restaurant
             ];
    }

  return response()->json(['data' => $data]);
}

function restaurantGetAllOrderByRestaurantId(){
  $restaurant = Auth::guard('restaurant-api')->user();
  $orders = DB::table('tb_orders')
  ->where('id_restaurant', '=', $restaurant->id)
  ->get();

  $data = [];

  foreach ($orders as $order) {
    $customer = User::where('id', '=', $order->id_user)
                  ->select('id', 'name', 'phone_number', 'address')
                  ->first();

    $order_foods = tb_order_food::where('id_order', '=', $order->id)->get();

    $data[] = ['id' => $order->id,
               'status' => $order->status,
               'price' => $order->price,
               'note' => $order->note,
               'delivery_fee' => $order->delivery_fee,
               'pickup_time' => $order->pickup_time,
               'order_type' => $order->order_type,
               'address' => $order->address,
               'foods' => $order_foods,
               'order_type' => $order->order_type,
               'created_at' => $order->created_at,
               'customer' => $customer
           ];
  }

  return response()->json(['data' => $data]);
}

function rejectOrderByOrderId(){
  $id_order = Request()->id_order;
  $price = tb_order::where('id', '=', $id_order)
  ->value('price');

  $id_user = tb_order::where('id', '=', $id_order)
  ->value('id_user');

  $balance_user = User::where('id', '=', $id_user)
  ->value('balance');

  $updated_balance = $price + $balance_user;

  User::where('id', '=', $id_user)
  ->update(['balance' => $updated_balance]);

  $reject = DB::table('tb_orders')
  ->where('id', $id_order)
  ->update(['status' => 'REJECTED']);

  $title = 'Yumm. it';
  $message = 'Your order has been rejected !';
  $this->broadcastMessageCustomer($title, $message, $id_order, $id_user);
  return response()->json(['rejected' => $reject]);
}

function acceptOrderByOrderId(){
  $id_order = Request()->id_order;
  $id_user = tb_order::where('id', '=', $id_order)
  ->value('id_user');

  $accept = DB::table('tb_orders')
  ->where('id', '=', $id_order)
  ->update(['status' => 'ON_PROGRESS']);

  $title = 'Yumm. it';
  $message = 'Your order has been accepted !';
  $this->broadcastMessageCustomer($title, $message, $id_order, $id_user);
  return response()->json(['accept' => $accept]);
}

function finishOrder(Request $req){
  $id_order = $req->id_order;

  $id_user = DB::table('tb_orders')
              ->where('id', '=', $id_order)
              ->value('id_user');

  $courrier = DB::table('courriers')
              ->where('status', '=', 'AVAILABLE')
              ->first();

  DB::table('courriers')
  ->where('id', '=', $courrier->id)
  ->update(['status' => 'BUSY']);

  $update = [
    'status' => 'ON_THE_WAY',
    'id_courrier' => $courrier->id
  ];

  DB::table('tb_orders')
  ->where('id', '=', $id_order)
  ->update($update);

  $title = 'Yumm. it';
  $message = 'Your order is on the way !';
  $this->broadcastMessageCustomer($title, $message, $id_order, $id_user);

  return response()->json(['data' => $courrier]);
}

function doneOrderByOrderId(){
  $user = Auth::user();
  $id_order = Request()->id_order;

  $id_courrier = DB::table('tb_orders')
                  ->where('id', '=', $id_order)
                  ->value('id_courrier');

  DB::table('courriers')
  ->where('id', '=', $id_courrier)
  ->update(['status' => 'AVAILABLE']);

  $id_user = tb_order::where('id', '=', $id_order)
  ->value('id_user');

  $restaurant_id = DB::table('tb_orders')
  ->where('id', $id_order)
  ->value('id_restaurant');

  $id_balance_history = tb_order::where('id', '=', $id_order)
  ->value('id_balance_history');

  $done = DB::table('tb_orders')
  ->where('id', $id_order)
  ->update(['status' => 'DONE']);

  $id_restaurant = DB::table('tb_orders')
  ->where('id', $id_order)
  ->first()
  ->id_restaurant;

  $orderPrice = DB::table('tb_orders')
  ->where('id', $id_order)
  ->first()
  ->price;

  $balanceRestaurant = DB::table('tb_restaurants')
  ->where('id', '=', $id_restaurant)
  ->first()
  ->balance;

  $finalBalanceRestaurant = $balanceRestaurant + $orderPrice;

  $updateBalanceRestaurant = DB::table('tb_restaurants')
  ->where('id', '=', $id_restaurant)
  ->update(['balance' => $finalBalanceRestaurant]);

  tb_balance_history::where('id', '=', $id_balance_history)
  ->update(['status' => 'COMPLETED']);

  $title = 'Yumm. it';
  $message = 'Order from ' . $user->name . ' has been arrived !';

  $this->broadcastMessageRestaurant($title, $message, $id_order, $restaurant_id);
  return response()->json(['message' => 'Order status DONE, Balance user & restaurant updated !']);
}

private function broadcastMessageRestaurant($title, $message, $id_order, $id_restaurant) {
  $optionBuilder = new OptionsBuilder();
  $optionBuilder->setTimeToLive(60*20);

  $notificationBuilder = new PayloadNotificationBuilder($title);
  $notificationBuilder->setBody($message)
  ->setSound('default');

  $dataBuilder = new PayloadDataBuilder();
  $dataBuilder->addData(['id_order' => $id_order]);

  $option = $optionBuilder->build();
  $notification = $notificationBuilder->build();
  $data = $dataBuilder->build();

  $token = tb_restaurant::where('id', '=', $id_restaurant)->value('fcm_token');

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
