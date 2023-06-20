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
use App\Models\Permission;
use App\Models\Quote;
use App\Models\ProductValue;
use App\Models\OrderForm;
use App\Models\Master;
use App\Models\QuoteProduct;
use App\Models\OrderedProduct;
use App\Traits\FileUpload;

use App\Models\ProductUserColumn;
use App\Jobs\UserEmailVerification;

class SettingController extends BaseController
{
	use FileUpload;

  public function getAccountInfo(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $childs = Recipienter::where('parent_id', $recipienter->id)->orderByDesc('created_at')->get();
    $permissions = Permission::where('recipienter_id', $recipienter->id)->get();
    
    \Payjp\Payjp::setApiKey('sk_live_c7d3d25dce6894e273b6467870d5a49449feea9147248392900c6b26');
    $tenant = \Payjp\Tenant::retrieve($recipienter->tenant_id);
    $review_url = \Payjp\Tenant::retrieve($recipienter->tenant_id)->application_urls->create();

    $reviewed_brands = array();
    foreach($tenant->reviewed_brands as $brand) {
      array_push($reviewed_brands, [
        'brand' => $brand->brand,
        'status' => $brand->status,
        'available_date' => $brand->available_date
      ]);
    }

    return response()->json([
      'recipienter' => $recipienter,
      'childs' => $childs,
      'permissions' => $permissions,
      'tenant' => [
        'name' => $tenant->name,
        'platform_fee_rate' => $tenant->platform_fee_rate,
        'minimum_transfer_amount' => $tenant->minimum_transfer_amount,
        'reviewed_brands' => $reviewed_brands
      ],
      'review_url' => $review_url->url
    ]);
  }

  public function updatePermission(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();

    $permission = Permission::where('recipienter_id', $recipienter->id)->where('moduleNum', $request->input('id'))->first();

    if (!is_null($permission)) {
      Permission::where('recipienter_id', $recipienter->id)->where('moduleNum', $request->input('id'))->update([
        'show' => $request->input('show') == 1 ? true : false,
        'control' => $request->input('control') == 1 ? true : false,
      ]);
    } else {
      Permission::create([
        'recipienter_id' => $recipienter->id,
        'show' => $request->input('show') == 1 ? true : false,
        'control' => $request->input('control') == 1 ? true : false,
        'moduleNum' => $request->input('id')
      ]);
    }

    return response()->json([
      'flag' => true
    ]);
  }

  public function editAccountInfo(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    Recipienter::where('id', $recipienter->id)->update([
      'name' => $request->input('name'),
      'zipcode' => $request->input('zipcode'),
      'prefecture' => $request->input('prefecture'),
      'city' => $request->input('city'),
      'address' => $request->input('address'),
      'phone' => $request->input('phone'),
    ]);

    return response()->json([
      'flag' => true
    ]);
  }

  public function saveStamp(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $stamp_url = $this->uploadFile($request->file('stamp'), 'upload');
    Recipienter::where('id', $recipienter->id)->update([
      'stamp' => $stamp_url
    ]);

    return response()->json([
      'flag' => true
    ]);
  }

  public function deleteStamp(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    Recipienter::where('id', $recipienter->id)->update([
      'stamp' => null
    ]);

    return response()->json([
      'flag' => true
    ]);
  }

  public function editAccountBankInfo(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    \Payjp\Payjp::setApiKey('sk_live_c7d3d25dce6894e273b6467870d5a49449feea9147248392900c6b26');
    if ($recipienter->tenant_id == null) {
      $tenant = \Payjp\Tenant::create(
        array(
          "name" => $recipienter->email,
          "platform_fee_rate" => "1.5",
          "minimum_transfer_amount" => 1000,
          "bank_account_holder_name" => $request->input('account_name'),
          "bank_code" => $request->input('bank_code'),
          "bank_branch_code" => $request->input('branch_code'),
          "bank_account_type" => $request->input('account_type') == 1 ? "普通" : ($request->input('account_type') == 2 ? "当座" : "貯蓄"),
          "bank_account_number" => $request->input('account_number')
        )
      );
      $recipienter->tenant_id = $tenant->id;
      $recipienter->save();
    } else {
      $te = \Payjp\Tenant::retrieve($recipienter->tenant_id);
      $te->bank_account_holder_name = $request->input('account_name');
      $te->bank_code = $request->input('bank_code');
      $te->bank_branch_code = $request->input('branch_code');
      $te->bank_account_type = $request->input('account_type') == 1 ? "普通" : ($request->input('account_type') == 2 ? "当座" : "貯蓄");
      $te->bank_account_number = $request->input('account_number');
      $te->save();
    }

    Recipienter::where('id', $recipienter->id)->update([
      'bank_code' => $request->input('bank_code'),
      'branch_code' => $request->input('branch_code'),
      'account_type' => $request->input('account_type'),
      'account_number' => $request->input('account_number'),
      'account_name' => $request->input('account_name')
    ]);

    return response()->json([
      'flag' => true
    ]);
  }

  public function createUserInfo(Request $request) {
    $length = 10;
    $token = substr(\str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, $length);

    $recipienter = Recipienter::where('email', $request->input('email'))->where('is_email_authenticated', 1)->first();

    if (!is_null($recipienter)) {
      return response()->json([
        'status' => 0
      ]);
    }

    Recipienter::where('email', $request->input('email'))->delete();

    $user = Auth::guard('recipienter')->user();
    $recipienter = Recipienter::create([
      'parent_id' => $user->id,
      'tanto_name' => $request->input('tanto_name'),
      'email' => $request->input('email'),
      'type' => 2,
      'token' => $token,
      'token_at' => Carbon::now()->addDay()
    ]);

    try {
      EmailVerification::dispatch($recipienter);
    } catch (Exception $e) {
      return false;
    }

    return response()->json([
      'status' => 1,
    ]);
  }

  public function deleteChildProc(Request $request) {
    Recipienter::where('id', $request->input('id'))->delete();

    return response()->json([
      'flag' => true
    ]);
  }

  public function getChildInfo(Request $request) {
    $user = Recipienter::where('id', $request->input('id'))->first();

    return response()->json([
      'user' => $user
    ]);
  }

  public function updateUserInfo(Request $request) {
    Recipienter::where('id', $request->input('userId'))->update([
      'tanto_name' => $request->input('tanto_name')
    ]);

    return response()->json([
      'flag' => true
    ]);
  }

  public function getIncomes(Request $request) {
    \Payjp\Payjp::setApiKey('sk_live_c7d3d25dce6894e273b6467870d5a49449feea9147248392900c6b26');
    $recipienter = Auth::guard('recipienter')->user();
    $transfer = \Payjp\TenantTransfer::all(array("tenant" => $recipienter->tenant_id));

    $res = [];
    foreach($transfer->data as $charge) {
      array_push($res, [
        'amount' => $charge->amount,
        'scheduled_date' => $charge->scheduled_date,
        'status' => $charge->status,
        'term_start' => $charge->term_start,
        'term_end' => $charge->term_end,
        'id' => $charge->id
      ]);
    }

    return response()->json([
      'res' => $res
    ]);
  }

  public function getPayments(Request $request) {
    \Payjp\Payjp::setApiKey('sk_live_c7d3d25dce6894e273b6467870d5a49449feea9147248392900c6b26');
    $charges = \Payjp\TenantTransfer::retrieve($request->input('id'))->charges->all();

    $res = [];
    foreach($charges->data as $charge) {
      array_push($res, [
        'amount' => $charge->amount,
        'captured_at' => $charge->captured_at,
        'id' => $charge->id,
        'paid' => $charge->paid,
        'refunded' => $charge->refunded
      ]);
    }

    return response()->json([
      'res' => $res
    ]);
  }

  public function getPaymentDetail(Request $request) {
    \Payjp\Payjp::setApiKey('sk_live_c7d3d25dce6894e273b6467870d5a49449feea9147248392900c6b26');
    $charge = \Payjp\Charge::retrieve($request->input('id'));
    $master = Master::first();
    $res = array([
      'id' => $charge->id,
      'paid' => $charge->paid,
      'refunded' => $charge->refunded,
      'fee_rate' => $charge->fee_rate,
      'amount' => $charge->amount,
      'captured_at' => $charge->captured_at,
      'card' => $charge->card->brand
    ]);

    return response()->json([
      'payment_detail' => $res,
      'master' => $master
    ]);
  }

  public function refundProc(Request $request) {
    \Payjp\Payjp::setApiKey('sk_live_c7d3d25dce6894e273b6467870d5a49449feea9147248392900c6b26');
    $ch = \Payjp\Charge::retrieve($request->input('id'));
    if ($request->input('type') == 1) {
      $ch->refund(array("refund_reason" => $request->input('refund_reason')));
    } else {
      $ch->refund(array("amount" => $request->input('price'), "refund_reason" => $request->input('refund_reason')));
    }

    return response()->json([
      'flag' => true
    ]);
  }
}