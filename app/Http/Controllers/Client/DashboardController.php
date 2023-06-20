<?php

namespace App\Http\Controllers\Client;

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
use App\Models\OrderForm;
use App\Models\ClientNews;
use App\Models\OrderFormMailTemplate;
use App\Models\OrderedProduct;
use App\Models\ProductValue;
use App\Traits\FileUpload;
use App\Models\ProductUserColumn;

class DashboardController extends BaseController
{
  use FileUpload;

  public function getDashboardData(Request $request) {
    $user = Auth::user();
    $news = ClientNews::where('user_id', $user->id)->where('readed', 0)->orderByDesc('created_at')->limit(10)->get();
    $orders = OrderFormMailTemplate::whereNotNull('ordered_at')
      ->where('ordered_at', '>=', date('Y-m-d').' 00:00:00')
      ->where('user_id', $user->id)->orderByDesc('ordered_at')->get();

    return response()->json([
      'news' => $news,
      'orders' => $orders
    ]);
  }

  public function editAccountInfo(Request $request) {
    $user = Auth::user();
    User::where('id', $user->id)->update([
      'com_name' => $request->input('com_name'),
      'zipcode' => $request->input('zipcode'),
      'prefecture' => $request->input('prefecture'),
      'city' => $request->input('city'),
      'building' => $request->input('building'),
      'telephone' => $request->input('telephone'),
    ]);

    return response()->json([
      'flag' => true
    ]);
  }
}