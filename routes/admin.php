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

Route::group(['middleware' => 'auth.admin:admin'], function () {
    Route::post('user', 'Auth\UserController@current');

    Route::post('logout', 'Auth\LoginController@logout');

    // ユーザー管理
    Route::post('/invite_master_account', 'UserController@inviteMasterAccount');
    Route::post('/get_recipienter_list', 'UserController@getRecipienterList');
    Route::post('/block_user', 'UserController@blockUser');
    Route::post('/delete_user', 'UserController@deleteUser');
    Route::post('/save_editing_recipienter_info', 'UserController@saveEditingRecipienterInfo');
    Route::post('/remove_client', 'UserController@removeClient');

    Route::post('/get_recipienter_detail', 'UserController@getRecipienterDetail');
    Route::post('/remove_recipienter', 'UserController@removeRecipienter');
    Route::post('/save_editing_client_info', 'UserController@saveEditingClientInfo');

    Route::post('/get_platform_info', 'UserController@getPlatformInfo');
    Route::post('/save_platform_info', 'UserController@savePlatformInfo');
});