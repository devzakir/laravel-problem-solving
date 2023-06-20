<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\InviteService;
use App\User;
use App\Models\Admin;
use App\Models\Master;
use App\Models\Recipienter;
use App\Jobs\EmailVerification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserController extends BaseController
{
    public function inviteMasterAccount(Request $request) {

      $length = 10;
      $token = substr(\str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, $length);

      $user = Recipienter::where('email', $request->input('email'))->where('is_email_authenticated', 1)->first();
      
      if (!is_null($user)) {
        return response()->json([
          'status' => 0
        ]);
      }

      Recipienter::where('email', $request->input('email'))->delete();
      $user = Recipienter::create([
        'type' => $request->input('type'),
        'email' => $request->input('email'),
        'name' => $request->input('name'),
        'token' => $token,
        'token_at' => Carbon::now()->addDay()
      ]);

      try {
        EmailVerification::dispatch($user);
      } catch (Exception $e) {
        return false;
      }

      return response()->json([
        'status' => 1,
      ]);
    }

    public function getRecipienterList(Request $request) {
      $query = Recipienter::orderByDesc('created_at', 'desc')
        ->where('name', 'LIKE', "%{$request->input('keyword')}%")
        ->where('email', 'LIKE', "%{$request->input('keyword')}%");
      if (!is_null($request->input('from'))) {
        $query->where('created_at', '>', $request->input('from'));
      } else if (!is_null($request->input('to'))) {
        $query->where('created_at', '<', $request->input('to'));
      }
      $recipienters = $query->with('childUsers', 'users')->get(); 

      return response()->json([
        'recipienters' => $recipienters
      ]);
    }

    public function blockUser(Request $request) {
      Recipienter::where('id', $request->input('user_id'))->update([
        'status' => $request->input('status')
      ]);
      $query = Recipienter::orderByDesc('created_at', 'desc')
        ->where('name', 'LIKE', "%{$request->input('keyword')}%")
        ->where('email', 'LIKE', "%{$request->input('keyword')}%");
      if (!is_null($request->input('from'))) {
        $query->where('created_at', '>', $request->input('from'));
      } else if (!is_null($request->input('to'))) {
        $query->where('created_at', '<', $request->input('to'));
      }
      $recipienters = $query->with('childUsers', 'users')->get(); 

      return response()->json([
        'recipienters' => $recipienters
      ]);
    }

    public function deleteUser(Request $request) {
      Recipienter::where('id', $request->input('user_id'))->delete();
      $recipienters = Recipienter::orderByDesc('created_at', 'desc')->with('childUsers', 'users')->get();

      return response()->json([
        'recipienters' => $recipienters
      ]);
    }

    public function getRecipienterDetail(Request $request) {
      $recipienter = Recipienter::where('id', $request->input('id'))->with('childUsers', 'users')->first();
      $childUsers = Recipienter::where('parent_id', $request->input('id'))->get();
      $self = Recipienter::where('id', $request->input('id'))->get();
      $merged = $self->merge($childUsers);
      return response()->json([
        'recipienter' => $recipienter,
        'recipienters' => $merged
      ]);
    }

    public function removeRecipienter(Request $request) {
      Recipienter::where('id', $request->input('id'))->delete();

      return response()->json([
        'flag' => true
      ]);
    }

    public function saveEditingRecipienterInfo(Request $request) {
      Recipienter::where('id', $request->input('id'))->update([
        'tanto_name' => $request->input('name')
      ]);

      if ($request->input('password') != null && $request->input('password') != '') {
        Recipienter::where('id', $request->input('id'))->update([
          'password' => bcrypt($request->input('password'))
        ]);
      }

      return response()->json([
        'flag' => true
      ]);
    }

    public function removeClient(Request $request) {
      User::where('id', $request->input('id'))->delete();

      return response()->json([
        'flag' => true
      ]);
    }

    public function saveEditingClientInfo(Request $request) {
      User::where('id', $request->input('id'))->update([
        'com_name' => $request->input('com_name')
      ]);

      return response()->json([
        'flag' => true
      ]);
    }

    public function getPlatformInfo(Request $request) {
      $platform = Master::first();

      return response()->json([
        'platform' => $platform
      ]);
    }

    public function savePlatformInfo(Request $request) {
      $platform = Master::first();
      $platform->platform_fee = $request->input('platform_fee');
      $platform->min_price = $request->input('min_price');
      $platform->save();

      $recipienters = Recipienter::whereNotNull('tenant_id')->get();
      \Payjp\Payjp::setApiKey('sk_live_c7d3d25dce6894e273b6467870d5a49449feea9147248392900c6b26');
      foreach($recipienters as $recipienter) {
        $tenant = \Payjp\Tenant::retrieve($recipienter->tenant_id);
        $tenant->platform_fee_rate = strval($request->input('platform_fee'));
        $tenant->minimum_transfer_amount = $request->input('min_price');
        $tenant->save();
      }

      return response()->json([
        'flag' => true
      ]);
    }
}
