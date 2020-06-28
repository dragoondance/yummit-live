<?php

namespace App\Http\Controllers;

use DB;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\tb_restaurant;
use Illuminate\Support\Facades\Hash;

class RestaurantController extends Controller
{
  function register(Request $request){
    $input = $request->all();
    $input['password'] = bcrypt($input['password']);
    $file = $request->file('restaurant_image');
    if ($file != null) {
      $fileName = $file->getClientOriginalName();
      $fileExtension = $file->getClientOriginalExtension();
      $finalFileName = $fileName . '_' . time() . '.' . $fileExtension;
      $file->move('restaurant_image', $finalFileName);
      $input['restaurant_image'] = url('restaurant_image/' . $finalFileName);
    }
    $fileRestaurantHeader = $request->file('restaurant_header');
    if ($fileRestaurantHeader != null) {
      $fileName = $fileRestaurantHeader->getClientOriginalName();
      $fileExtension = $fileRestaurantHeader->getClientOriginalExtension();
      $finalFileNameRestaurantHeader = $fileName . '_' . time() . '.' . $fileExtension;
      $fileRestaurantHeader->move('restaurant_header', $finalFileNameRestaurantHeader);
      $input['restaurant_header'] = url('restaurant_header/' . $finalFileNameRestaurantHeader);
    }
    $user = tb_restaurant::create($input);
    $success['token'] =  $user->createToken('Restaurant')-> accessToken;
    $success['name'] =  $user->name;
    $success['email'] = $user->email;
    return response()->json(['success'=>$success]);
  }

  public function details(){
    $id_user = Auth::guard('restaurant-api')->user()->id;
    $detail = DB::table('tb_restaurants')
              ->where('tb_restaurants.id', '=', $id_user)
              ->leftJoin('tb_restaurant_categories', 'tb_restaurant_categories.id', '=', 'tb_restaurants.id_restaurant_category')
              ->select('tb_restaurants.id', 'tb_restaurants.name', 'tb_restaurants.email', 'tb_restaurants.phone_number', 'tb_restaurants.restaurant_image',
                       'tb_restaurants.restaurant_header', 'tb_restaurants.address', 'tb_restaurants.open_time', 'tb_restaurants.close_time',
                       'tb_restaurants.description', 'tb_restaurants.rating', 'tb_restaurants.balance', 'tb_restaurants.latitude', 'tb_restaurants.longitude',
                       'tb_restaurant_categories.name as restaurant_category_name')
              ->first();
    return response()->json(['success' => $detail]);
  }

  function login(Request $req) {
    if(Auth::guard('restaurant-web')->attempt(['email' => request('email'), 'password' => request('password')])){
      $user = Auth::guard('restaurant-web')->user();
      $success['token'] =  $user->createToken('Restaurant')->accessToken;
      $success['name'] = $user->name;
      $success['id'] = $user->id;

      DB::table('tb_restaurants')
      ->where('email', '=', request('email'))
      ->update(['fcm_token' => $req->fcm_token]);

      return response()->json(['success' => $success]);
    }
    else{
      return response()->json(['error'=>'UNAUTHORISED'], 401);
    }
  }

  public function changePassword(Request $request){
    $validator = Validator::make($request->all(), [
      'password' => 'required',
      'new_password' => 'required',
      'c_new_password' => 'required|same:new_password',
    ]);
    if ($validator->fails()) {
      return response()->json(['error'=>'UNAUTHORISED']);
    } else {
      if(Hash::check($request->password, Auth::guard('restaurant-api')->user()->password)){
        $user = Auth::guard('restaurant-api')->user();
        $user->password = bcrypt($request->new_password);
        $user->save();
        return response()->json(['success' => 'PASSWORD HAS BEEN CHANGED']);
      } else {
        return response()->json(['error' => 'WRONG PASSWORD']);
      }
    }
  }

  public function readAll(){
    try {
      $restaurants = DB::table('tb_restaurants')
                    ->leftJoin('tb_favorite_restaurants', function ($join) {
                      $join->on('tb_favorite_restaurants.id_restaurant', '=', 'tb_restaurants.id');
                      $join->where('tb_favorite_restaurants.id_user', '=', Auth::user()->id);
                    })
                    ->leftJoin('tb_restaurant_categories', 'tb_restaurant_categories.id', '=', 'tb_restaurants.id_restaurant_category')
                    ->select('tb_restaurants.id', 'tb_restaurants.name', 'tb_restaurants.phone_number', 'tb_restaurants.restaurant_image',
                             'tb_restaurants.restaurant_header', 'tb_restaurants.address', 'tb_restaurants.open_time',
                             'tb_restaurants.close_time', 'tb_restaurants.description', 'tb_restaurants.rating', 'tb_restaurants.latitude',
                             'tb_restaurants.longitude', 'tb_favorite_restaurants.id_restaurant as is_favorite', 'tb_restaurant_categories.name as category_name')
                    ->get();

        foreach ($restaurants as $restaurant) {
          if ($restaurant->is_favorite == null) {
            $restaurant->is_favorite = 'FALSE';
          } else {
            $restaurant->is_favorite = 'TRUE';
          }
          $restaurant = [
            'id' => $restaurant->id,
            'name' => $restaurant->name,
            'phone_number' => $restaurant->phone_number,
            'restaurant_image' => $restaurant->restaurant_image,
            'restaurant_header' => $restaurant->restaurant_header,
            'address' => $restaurant->address,
            'open_time' => $restaurant->open_time,
            'close_time' => $restaurant->close_time,
            'description' => $restaurant->description,
            'rating' => $restaurant->rating,
            'latitude' => $restaurant->latitude,
            'longitude' => $restaurant->longitude,
            'is_favorite' => $restaurant->is_favorite,
            'category_name' => $restaurant->category_name
          ];
        }

        return response()->json(['data' => $restaurants]);
    } catch (\Exception $e) {
      return response()->json(['error' => $e]);
    }
  }

  public function readRestaurantDetailByRestaurantId(Request $req){
    $idRest = $req->id_restaurant;
    $detail = DB::table('tb_restaurants')
                ->where('id','=', $idRest)
                ->select('id', 'name', 'phone_number', 'restaurant_image', 'restaurant_header', 'address', 'description',
                         'open_time', 'close_time', 'rating', 'latitude', 'longitude')
                ->first();
    return response()->json(['data' => $detail]);
  }

  public function updateRestaurant(Request $req){
      $user = Auth::guard('restaurant-api')->user();

      $fileRestaurantImage = $req->file('restaurant_image');
      $fileRestaurantHeader = $req->file('restaurant_header');
      if ($fileRestaurantImage != null) {
        $fileName = $fileRestaurantImage->getClientOriginalName();
        $fileExtension = $fileRestaurantImage->getClientOriginalExtension();
        $finalFileNameRestaurantImage = $fileName . '_' . time() . '.' . $fileExtension;
        $fileRestaurantImage->move('restaurant_image', $finalFileNameRestaurantImage);
      }

      if ($fileRestaurantHeader != null) {
        $fileName = $fileRestaurantHeader->getClientOriginalName();
        $fileExtension = $fileRestaurantHeader->getClientOriginalExtension();
        $finalFileNameRestaurantHeader = $fileName . '_' . time() . '.' . $fileExtension;
        $fileRestaurantHeader->move('restaurant_header', $finalFileNameRestaurantHeader);
      }

      $updateDetails = ['name' => $req->name,
                        'email' => $req->email,
                        'phone_number' => $req->phone_number,
                        'restaurant_image' => url('restaurant_image/' . $finalFileNameRestaurantImage),
                        'restaurant_header' => url('restaurant_header/' . $finalFileNameRestaurantHeader),
                        'address' => $req->address,
                        'open_time' => $req->open_time,
                        'close_time' => $req->close_time,
                        'description' => $req->description,
                        'id_restaurant_category' => $req->id_restaurant_category,
                        'latitude' => $req->latitude,
                        'longitude' => $req->longitude];

      $update = DB::table('tb_restaurants')
                ->where('id', '=', $user->id)
                ->update($updateDetails);
      return response()->json(['success' => $updateDetails]);
  }

  public function logout(Request $req){
    $user = Auth::guard('restaurant-api')->user();
    $user->token()->revoke();

    return response()->json([
      'message' => 'Successfully logged out'
    ]);
  }
}
