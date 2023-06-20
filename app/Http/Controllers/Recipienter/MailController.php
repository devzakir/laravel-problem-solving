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
use App\Models\ClientNews;
use App\Models\OrderFormMailTemplate;
use App\Models\OrderedProduct;
use App\Models\OrderMessage;
use App\Models\InvoiceMessage;
use App\Models\MailHistory;
use App\Traits\FileUpload;
use App\Models\ProductUserColumn;
use App\Jobs\OrderFormEmailJob;
use App\Jobs\OrderMessageJob;
use App\Jobs\InvoiceMessageJob;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;

class MailController extends BaseController
{
  public function getMailHistories(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $histories = MailHistory::where('recipienter_id', $recipienter->id)->with('user', 'recipienter')->orderByDesc('created_at')->get();

    return response()->json([
      'histories' => $histories
    ]);
  }

  public function getMailHistoryDetail(Request $request) {
    $mail = MailHistory::where('id', $request->input('id'))->with('user', 'recipienter')->first();

    return response()->json([
      'mail' => $mail
    ]);
  }
}