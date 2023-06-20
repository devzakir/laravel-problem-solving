<?php

use Illuminate\Support\Facades\Route;

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
Route::group(['middleware' => 'auth:api'], function () {
  Route::post('logout', 'Client\Auth\LoginController@logout');

  Route::get('/user', 'Client\Auth\UserController@current');
  Route::post('get_order_form', 'Client\OrderController@getOrderForm');
  Route::post('order_confirm_proc', 'Client\OrderController@orderConfirmProc');
  Route::post('get_order_form_detail', 'Client\OrderController@getOrderFormDetail');
  Route::post('get_order_detail', 'Client\OrderController@getOrderDetail');
  Route::post('provisional_payment', 'Client\OrderController@provisionalPayment');
  Route::post('bank_payment_proc', 'Client\OrderController@bankPaymentProc');
  Route::post('send_order_proc', 'Client\OrderController@sendOrderProc');
  Route::post('save_order_product', 'Client\OrderController@saveOrderProduct');

  Route::post('get_order_history', 'Client\OrderController@getOrderHistory');
  Route::post('get_shippinged_order', 'Client\OrderController@getShippingOrder');
  Route::post('delivery_confirm_proc', 'Client\OrderController@deliveryConfirmProc');
  Route::post('get_dashbaord_data', 'Client\DashboardController@getDashboardData');
  Route::post('edit_account_info', 'Client\DashboardController@editAccountInfo');
});

Route::group(['middleware' => 'guest:api'], function() {
  Route::post('admin_login', 'Admin\Auth\LoginController@login');

  Route::post('activate_email', 'ProfileController@activateEmail');
  Route::post('set_password', 'ProfileController@setPassword');

  // recipient authenticate
  Route::post('recipienter_login', 'Recipienter\Auth\LoginController@login');
  // client authenticate
  Route::post('client_login', 'Client\Auth\LoginController@login');
});