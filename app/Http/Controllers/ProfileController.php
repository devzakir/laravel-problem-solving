<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Traits\FileUpload;
use App\User;
use App\Models\Recipienter;
use App\Models\Master;
use Carbon\Carbon;
use Laravel\Cashier\Cashier;

class ProfileController extends Controller
{
  use FileUpload;

  public function activateEmail(Request $request) {
    $email = $request->input('email');
    $token = $request->input('token');
    $type = $request->input('type');

    if ($type == 1) {
      $user = Recipienter::where('email', $email)->first();
    } else {
      $user = User::where('email', $email)->first();
    }
    
    if(is_null($user)) {
        return response()->json([
            'status' => 0
        ]);
    }
    if (!is_null($user->email_verified_at)) {
        return response()->json([
            'status' => 1
        ]);
    }
    if ($user->token != $token) {
        return response()->json([
            'status' => 2
        ]);
    }
    if ($user->token_at < Carbon::now()) {
        return response()->json([
            'status' => 3
        ]);
    }
    if ($type == 1) {
      Recipienter::where('email', $email)->update([
        'email_verified_at' => Carbon::now(),
        'is_email_authenticated' => 1,
        'token' => null,
        'token_at' => null
      ]);
      $user = Recipienter::where('id', $user->id)->first();
    } else {
      User::where('email', $email)->update([
        'email_verified_at' => Carbon::now(),
        'is_email_authenticated' => 1,
        'token' => null,
        'token_at' => null
      ]);
      $user = User::where('id', $user->id)->first();
    }
    
    if ($user->token_at < Carbon::now()) {
        return response()->json([
            'status' => 4,
            'user_id' => $user->id
        ]);
    }
  }

  public function setPassword(Request $request) {
    $email = $request->input('email');
    $password = $request->input('password');
    $type = $request->input('type');
    if ($type == 1) {
      $recipienter = Recipienter::where('email', $email)->first();
      \Payjp\Payjp::setApiKey('sk_live_c7d3d25dce6894e273b6467870d5a49449feea9147248392900c6b26');
      $master = Master::first();
      $tenant = \Payjp\Tenant::create(
        array(
          "name" => $email,
          "platform_fee_rate" => strval($master->platform_fee),
          "minimum_transfer_amount" => $master->min_price,
        )
      );
      Recipienter::where('email', $email)->update([
        'password' => bcrypt($password),
        'tenant_id' => $tenant->id
      ]);
    } else {
      User::where('email', $email)->update([
        'password' => bcrypt($password)
      ]);
    }

    return response()->json([
      'flag' => true
    ]);
  }
}