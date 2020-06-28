<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['json.response']], function () {
  Route::get('currentTime', 'Controller@currentTime');
  Route::post('restaurant/registerRestaurant', 'RestaurantController@register');
  Route::post('restaurant/loginRestaurant', 'RestaurantController@login');
  Route::get('restaurant/readRestaurantCategory', 'RestaurantCategoryController@readRestaurantCategory');
  Route::post('register', 'API\UserController@register');
  Route::post('login', 'API\UserController@login');
  Route::post('admin/register', 'AdminController@register');
  Route::post('admin/login', 'AdminController@login');
  Route::post('courrier/register', 'CourrierController@register');
  Route::get('courrier/readAllCourrier', 'CourrierController@readAllCourrier');
  Route::get('courrier/readCourrierById', 'CourrierController@readCourrierById');
  Route::patch('courrier/update', 'CourrierController@update');
  Route::get('readRestaurantDetailByRestaurantId', 'RestaurantController@readRestaurantDetailByRestaurantId');
  Route::group(['middleware' => 'auth.admin'], function(){
    Route::post('admin/createRestaurantCategory', 'RestaurantCategoryController@create');
    Route::post('admin/ct/{id}', 'AdminController@ct');
    Route::patch('admin/changePassword', 'AdminController@changePassword');
    Route::get('admin/readAllTopupPending', 'AdminController@readAllTopupPending');
    Route::patch('admin/acceptTopup', 'AdminController@acceptTopup');
    Route::patch('admin/rejectTopup', 'AdminController@rejectTopup');});
  Route::group(['middleware' => 'auth.restaurant'], function(){
    Route::patch('restaurant/updateRestaurant', 'RestaurantController@updateRestaurant');
    Route::patch('restaurant/changePassword', 'RestaurantController@changePassword');
    Route::post('restaurant/detailsRestaurant', 'RestaurantController@details');
    Route::get('restaurant/logout', 'RestaurantController@logout');
    Route::post('restaurant/createWithdraw', 'RestaurantWithdrawController@create');
    Route::get('restaurant/readAllByRestaurantId', 'RestaurantWithdrawController@readAllByRestaurantId');
    Route::delete('restaurant/deleteWithdraw', 'RestaurantWithdrawController@delete');
    Route::get('restaurant/restaurantGetAllOrderByRestaurantId', 'OrderController@restaurantGetAllOrderByRestaurantId');
    Route::post('restaurant/createFood', 'FoodController@create');
    Route::patch('restaurant/updateFoodByFoodId', 'FoodController@update');
    Route::delete('restaurant/deleteFoodByFoodId', 'FoodController@delete');
    Route::post('restaurant/createFoodCategory', 'FoodCategoryController@create');
    Route::delete('restaurant/deleteFoodCategory', 'FoodCategoryController@delete');
    Route::get('restaurant/readAllOrderFoodByOrderId', 'OrderFoodController@readAllOrderFoodByOrderId');
    Route::get('restaurant/readFoodByRestaurantId', 'FoodController@restaurantReadByRestaurantId');
    Route::patch('restaurant/acceptOrderByOrderId', 'OrderController@acceptOrderByOrderId');
    Route::patch('restaurant/rejectOrderByOrderId', 'OrderController@rejectOrderByOrderId');
    Route::patch('restaurant/finishOrder', 'OrderController@finishOrder');
    Route::post('restaurant/createDiscount', 'DiscountController@create');
    Route::get('restaurant/readDiscountByFoodId', 'DiscountController@readDiscountByFoodId');
    Route::get('restaurant/readDiscountByRestaurantId', 'DiscountController@readDiscountByRestaurantId');
    Route::patch('restaurant/editDiscountByDiscountId', 'DiscountController@editDiscountByDiscountId');});
  Route::group(['middleware' => 'auth:api'], function(){
    Route::get('customer/logout', 'API\UserController@logout');
    Route::get('customer/details', 'API\UserController@details');
    Route::patch('customer/update', 'API\UserController@update');
    Route::patch('customer/updateBalance', 'API\UserController@updateBalance');
    Route::patch('customer/changePassword', 'API\UserController@changePassword');
    Route::get('customer/readRestaurantByCategory', 'RestaurantCategoryController@readRestaurantByCategory');
    Route::post('customer/createTopup', 'TopupController@create');
    Route::patch('customer/updateSlipImage', 'TopupController@updateSlipImage');
    Route::post('customer/createBalanceHistory', 'BalanceHistoryController@create');
    Route::get('customer/readBalanceHistoryByUserId', 'BalanceHistoryController@readByUserId');
    Route::post('customer/createFavoriteFood', 'FavoriteFoodController@create');
    Route::get('customer/readFavoriteFoodByUserId', 'FavoriteFoodController@readByUserId');
    Route::delete('customer/deleteFavoriteFoodById', 'FavoriteFoodController@deleteById');
    Route::post('customer/createFavoriteRestaurant', 'FavoriteRestaurantController@create');
    Route::get('customer/readFavoriteRestaurantByUserId', 'FavoriteRestaurantController@readByUserId');
    Route::delete('customer/deleteFavoriteRestaurantById', 'FavoriteRestaurantController@deleteById');
    Route::post('customer/createOrder', 'OrderController@create');
    Route::get('customer/readAllOrderByUserId', 'OrderController@readAllOrderByUserId');
    Route::patch('customer/doneOrderByOrderId', 'OrderController@doneOrderByOrderId');
    Route::post('customer/createOrderFood', 'OrderFoodController@create');
    Route::get('customer/readAllOrderFoodByOrderId', 'OrderFoodController@readAllOrderFoodByOrderId');
    Route::get('customer/readFoodByRestaurantId', 'FoodController@customerReadByRestaurantId');
    Route::get('customer/readAllRestaurant', 'RestaurantController@readAll');
    Route::get('customer/readRestaurantDetailByRestaurantId', 'RestaurantController@readRestaurantDetailByRestaurantId');
    Route::get('customer/readDiscountByFoodId', 'DiscountController@readDiscountByFoodId');
    Route::get('customer/readDiscountByRestaurantId', 'DiscountController@readDiscountByRestaurantId');});});
