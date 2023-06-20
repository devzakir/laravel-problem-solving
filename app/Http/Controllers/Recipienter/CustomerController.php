<?php

namespace App\Http\Controllers\Recipienter;

use Illuminate\Http\Request;
use App\Services\InviteService;
use App\Jobs\EmailVerification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\User;
use App\Models\Admin;
use App\Models\Recipienter;
use App\Models\ProductColumn;
use App\Models\Product;
use App\Models\ProductValue;
use App\Models\OrderForm;
use App\Models\OrderedProduct;
use App\Traits\FileUpload;
use App\Models\ProductUserColumn;
use App\Jobs\UserEmailVerification;

class CustomerController extends BaseController
{
	use FileUpload;

  public function getCustomerCreateInit(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $recipienters = Recipienter::where('parent_id', $recipienter->id)->get();
    return response()->json([
      'recipienters' => $recipienters
    ]);
  }

  public function getCustomerEditInit(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $recipienters = Recipienter::where('parent_id', $recipienter->id)->get();
    $user = User::where('id', $request->input('id'))->first();
    return response()->json([
      'recipienters' => $recipienters,
      'user' => $user
    ]);
  }

  public function inviteUser(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $length = 10;
    $token = substr(\str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, $length);

    $user = User::where('email', $request->input('email'))->where('is_email_authenticated', 1)->first();
    if (!is_null($user)) {
      return response()->json([
        'status' => 0
      ]);
    }
    User::where('email', $request->input('email'))->delete();
    $user = User::create([
      'com_name' => $request->input('com_name'),
      'name' => $request->input('name'),
      'department_name' => $request->input('department_name'),
      'telephone' => $request->input('telephone'),
      'email' => $request->input('email'),
      'recipienter_id' => is_null($request->input('recipienter_id')) ? $recipienter->id : $request->input('recipienter_id'),
      'token' => $token,
      'token_at' => Carbon::now()->addDay()
    ]);

    try {
      UserEmailVerification::dispatch($user);
    } catch (Exception $e) {
      return false;
    }

    return response()->json([
      'status' => 1,
    ]);
  }

  public function getCustomers(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $query = User::where('recipienter_id', $recipienter->id);
    
    // 検索クエリ
    if (!is_null($request->input('keyword'))) {
      $keyword = $request->input('keyword');
      $query->where(function($q) use ($keyword) {
        $q->where('com_name', 'like', '%'.$keyword.'%');
        $q->orWhere('department_name', 'like', '%'.$keyword.'%');
        $q->orWhere('name', 'like', '%'.$keyword.'%');
        $q->orWhere('telephone', 'like', '%'.$keyword.'%');
      });
    }
    if (!is_null($request->input('fromDate'))) {
      $query->where('created_at', '>=', $request->input('fromDate'));
    }
    if (!is_null($request->input('toDate'))) {
      $query->where('created_at', '<=', $request->input('toDate'));
    }
    
    $users = $query->orderByDesc('created_at')->get();

    return response()->json([
      'users' => $users
    ]);
  }

  public function deleteCustomer(Request $request) {
    User::where('id', $request->input('id'))->delete();

    return response()->json([
      'flag' => true
    ]);
  }

  public function updateUser(Request $request) {
    User::where('id', $request->input('id'))
      ->update([
        'com_name' => $request->input('com_name'),
        'department_name' => $request->input('department_name'),
        'name' => $request->input('name'),
        'telephone' => $request->input('telephone'),
      ]);
    
    return response()->json([
      'flag' => true
    ]);
  }
}