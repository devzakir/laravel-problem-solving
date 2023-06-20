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
use App\Models\News;
use App\Models\OrderFormMailTemplate;
use App\Models\OrderedProduct;
use App\Traits\FileUpload;
use App\Models\ProductUserColumn;
use App\Jobs\OrderFormEmailJob;

class DashboardController extends BaseController
{
	use FileUpload;

  public function getDashboardData(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $news = News::where('recipienter_id', $recipienter->id)->where('readed', 0)->orderByDesc('created_at')->limit(10)->get();
    $orders = OrderFormMailTemplate::whereNotNull('ordered_at')
      ->where('ordered_at', '>=', date('Y-m-d').' 00:00:00')
      ->where('recipienter_id', $recipienter->id)->orderByDesc('ordered_at')->get();

    return response()->json([
      'news' => $news,
      'orders' => $orders,
    ]);
  }

  public function getOrderId(Request $request) {
    $order = OrderFormMailTemplate::where('hash', $request->input('hash'))->first();

    return response()->json([
      'id' => $order->id
    ]);
  }
}