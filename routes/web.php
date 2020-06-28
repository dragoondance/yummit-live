<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return view('welcome');
});
//Login
Route::get('/admin/login', 'AdminController@showLoginPageWeb')->name('admin-login');
Route::post('/admin/login', 'AdminController@loginWeb');
//Logout
Route::get('/admin/logout', 'AdminController@logoutWeb')->middleware('auth.admin')->name('admin-logout');
//Topup
Route::get('/admin/topup', 'AdminController@showTopupPageWeb')->middleware('auth.admin')->name('admin-topup');
Route::get('/admin/acceptTopupWeb/{id}', 'AdminController@acceptTopupWeb');
Route::get('/admin/rejectTopupWeb/{id}', 'AdminController@rejectTopupWeb');
//Order
Route::get('/admin/order', 'AdminController@showOrderPageWeb')->middleware('auth.admin')->name('admin-order');
Route::get('/admin/acceptOrderWeb/{id}', 'AdminController@acceptOrderWeb');
Route::get('/admin/rejectOrderWeb/{id}', 'AdminController@rejectOrderWeb');
//Withdraw
Route::get('/admin/withdraw', 'AdminController@showWithdrawPageWeb')->middleware('auth.admin')->name('admin-withdraw');
Route::get('/admin/acceptWithdrawWeb/{id}', 'AdminController@acceptWithdrawWeb');
Route::get('/admin/rejectWithdrawWeb/{id}', 'AdminController@rejectWithdrawWeb');
