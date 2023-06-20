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
use App\Models\Quote;
use App\Models\ProductValue;
use App\Models\OrderForm;
use App\Models\QuoteProduct;
use App\Models\OrderedProduct;
use App\Traits\FileUpload;

use App\Models\ProductUserColumn;
use App\Jobs\UserEmailVerification;
use App\Jobs\QuoteEmailJob;

class QuoteController extends BaseController
{
	use FileUpload;

  public function getQuoteCreateInit(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $users = User::where('recipienter_id', $recipienter->id)->get();

    return response()->json([
      'users' => $users
    ]);
  }

  public function getClientList(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $query = User::where('recipienter_id', $recipienter->id);

    if (!is_null($request->input('keyword'))) {
			$keyword = $request->input('keyword');
			$query->where(function($qry) use ($keyword) {
        $qry->where('email', 'like', '%'.$keyword.'%')
          ->orWhere('name', 'like', '%'.$keyword.'%')
          ->orWhere('com_name', 'like', '%'.$keyword.'%')
          ->orWhere('department_name', 'like', '%'.$keyword.'%');
      });
		}
    $users = $query->orderByDesc('created_at')->get();

    return response()->json([
      'users' => $users
    ]);
  }

  public function saveQuote(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $length = 10;
    $token = substr(\str_shuffle('1234567890'), 1, $length);
    $hash = 'XSAD-'.$token;

    $quote = Quote::create([
      'subject_title' => $request->input('subject_title'),
      'publish_date' => $request->input('publish_date'),
      'hash' => $hash,
      'email' => $request->input('email'),
      'com_name' => $request->input('com_name'),
      'department_name' => $request->input('department_name'),
      'name' => $request->input('name'),
      'recipienter_id' => $recipienter->id
    ]);

    return response()->json([
      'quote' => $quote
    ]);
  }

  public function saveQuoteProduct(Request $request) {
    QuoteProduct::create([
      'name' => $request->input('name'),
      'quote_id' => $request->input('quoteId'),
      'price' => $request->input('price'),
      'amount' => $request->input('amount'),
      'total_price' => $request->input('total_price'),
      'remark' => $request->input('remark'),
      'tax' => $request->input('tax')
    ]);
    
    return response()->json([
      'flag' => true
    ]);
  }

  public function getQuoteListInfo(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $query = Quote::where('recipienter_id', $recipienter->id);

    if (!is_null($request->input('keyword'))) {
      $keyword = $request->input('keyword');
      $query = Quote::where(function($qry) use ($keyword) {
        $qry->where('subject_title', 'like', '%'.$keyword.'%')
          ->orWhere('com_name', 'like', '%'.$keyword.'%')
          ->orWhere('department_name', 'like', '%'.$keyword.'%');
      });
    }
    
    if (!is_null($request->input('fromDate'))) {
      $query->where('publish_date', '>=', $request->input('fromDate'));
    }

    if (!is_null($request->input('toDate'))) {
      $query->where('publish_date', '<=', $request->input('toDate'));
    }

    $quotes = $query->orderByDesc('created_at')->get();
    return response()->json([
      'quotes' => $quotes
    ]);
  }

  public function getQuoteDetailInfo(Request $request) {
    $quote = Quote::where('id', $request->input('id'))->with('products')->first();

    return response()->json([
      'quote' => $quote
    ]);
  }

  public function sendQuoteEmail(Request $request) {
    Quote::where('id', $request->input('id'))->update([
      'status' => 1
    ]);

    $quote = Quote::where('id', $request->input('id'))->with('recipienter')->first();
    $pdf_url = $this->uploadFile($request->file('pdf'), 'upload');
    try {
      QuoteEmailJob::dispatch($quote, 'https://ukerundesu.com/storage/'.$pdf_url);
    } catch (Exception $e) {
      return false;
    }

    return response()->json([
      'flag' => true
    ]);
  }

  public function quoteDeleteProc(Request $request) {
    Quote::where('id', $request->input('id'))->delete();

    return response()->json([
      'flag' => true
    ]);
  }

  public function getQuoteEditInit(Request $request) {
    $quote = Quote::where('id', $request->input('id'))->first();

    $quote_products = QuoteProduct::where('quote_id', $request->input('id'))->get();

    return response()->json([
      'quote' => $quote,
      'quote_products' => $quote_products
    ]);
  }

  public function updateQuote(Request $request) {
    QuoteProduct::where('quote_id', $request->input('id'))->delete();

    return response()->json([
      'flag' => true
    ]);
  }
}