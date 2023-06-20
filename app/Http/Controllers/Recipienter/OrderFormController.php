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
use App\Models\MailHistory;
use App\Models\OrderFormMessage;
use App\Models\ClientNews;
use App\Models\OrderFormMailTemplate;
use App\Models\OrderedProduct;
use App\Traits\FileUpload;
use App\Models\ProductUserColumn;
use App\Jobs\OrderFormEmailJob;

class OrderFormController extends BaseController
{
	use FileUpload;

  public function createOrderForm(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    OrderForm::create([
      'name' => $request->input('name'),
      'condition' => $request->input('condition'),
      'has_deadline' => $request->input('has_deadline'),
      'payment_method' => $request->input('payment_method'),
      'recipienter_id' => $recipienter->id,
      'memo' => $request->input('memo')
    ]);

    return response()->json([
      'flag' => true
    ]);
  }

  public function getOrderFormList(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $orderForms = OrderForm::where('recipienter_id', $recipienter->id)->with('orderedProducts')->orderByDesc('created_at')->get();

    return response()->json([
      'orderForms' => $orderForms
    ]);
  }

  public function getOrderFormDetail(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $orderForm = OrderForm::where('id', $request->input('id'))->with('orderedProducts')->first();
    $order_form_id = $request->input('id');
    $orderedProducts = Product::whereIn('id', function($query) use ($order_form_id) {
      $query->select('product_id')
        ->from(with(new OrderedProduct)->getTable())
        ->where('order_form_id', $order_form_id);
    })->with('productValues')->get();
    $columns = ProductColumn::orderBy('order')->get();
		$userColumns = ProductUserColumn::where('recipienter_id', $recipienter->id)->with('productColumn')->get();

    return response()->json([
      'orderForm' => $orderForm,
      'orderedProducts' => $orderedProducts,
      'columns' => $columns,
      'userColumns' => $userColumns
    ]);
  }

  public function appendOrderedFormProduct(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    OrderedProduct::create([
      'product_id' => $request->input('id'),
      'order_form_id' => $request->input('order_form_id'),
      'recipienter_id' => $recipienter->id
    ]);

    return response()->json([
      'flag' => true
    ]);
  }

  public function cancelOrderedProduct(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    OrderedProduct::where('product_id', $request->input('product_id'))
      ->where('order_form_id', $request->input('id'))
      ->where('recipienter_id', $recipienter->id)->delete();

    return response()->json([
      'flag' => true
    ]);
  }

  public function getOrderFormInfo(Request $request) {
    $order_form = OrderForm::where('id', $request->input('id'))->first();

    return response()->json([
      'order_form' => $order_form
    ]);
  }

  public function editOrderForm(Request $request) {
    OrderForm::where('id', $request->input('id'))->update([
      'name' => $request->input('name'),
      'condition' => $request->input('condition'),
      'has_deadline' => $request->input('has_deadline'),
      'payment_method' => $request->input('payment_method'),
      'memo' => $request->input('memo')
    ]);

    return response()->json([
      'flag' => true
    ]);
  }

  public function deleteOrderForm(Request $request) {
    OrderForm::where('id', $request->input('id'))->delete();

    return response()->json([
      'flag' => true
    ]);
  }

  public function getMessageCreateScreenInit(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $childRecipienters = Recipienter::where('parent_id', $recipienter->id)->get();
    $order_form = OrderForm::where('id', $request->input('id'))->first();
    $order_forms = OrderForm::where('recipienter_id', $recipienter->id)->get();
    return response()->json([
      'childRecipienters' => $childRecipienters,
      'order_form' => $order_form,
      'order_forms' => $order_forms
    ]);
  }

  public function getSelfClients(Request $request) {
    $recipienter_id = $request->input('recipienter_id');

    $users = User::where('recipienter_id', $recipienter_id)->get();
    $usersFromChildrens = User::whereIn('recipienter_id', function($query) use ($recipienter_id) {
      $query->select('recipienter_id')
        ->from(with(new Recipienter)->getTable())
        ->where('parent_id', $recipienter_id);
    })->get();
    $merged = $users->merge($usersFromChildrens);

    return response()->json([
      'users' => $merged
    ]);
  }

  public function createOrderFormMessage(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $length = 10;
    $token = substr(\str_shuffle('1234567890'), 1, $length);
    $hash = 'XSAD-'.$token;

    // $orderFormEmail = OrderFormMailTemplate::create([
    //   'recipienter_id' => $request->input('recipienter_id'),
    //   'user_name' => $request->input('user_name'),
    //   'user_id' => $request->input('user_id'),
    //   'title' => $request->input('title'),
    //   'content' => $request->input('content'),
    //   'address_info' => $request->input('address_info'),
    //   'order_form_id' => $request->input('order_form_id'),
    //   'hash' => $hash
    // ]);

    MailHistory::create([
      'title' => $request->input('title'),
      'user_id' => $request->input('user_id'),
      'recipienter_id' => $recipienter->id,
      'mail_type' => 3,
      'message' => $request->input('content'),
      'address' => $request->input('address_info')
    ]);

    $orderFormMessage = OrderFormMessage::create([
      'title' => $request->input('title'),
      'message' => $request->input('content'),
      'address' => $request->input('address_info'),
      'recipienter_id' => $recipienter->id,
      'client_id' => $request->input('user_id'),
      'order_form_id' => $request->input('order_form_id')
    ]);

    $user = User::where('id', $request->input('user_id'))->first();
    try {
      OrderFormEmailJob::dispatch($orderFormMessage, $user);
    } catch (Exception $e) {
      return false;
    }

    ClientNews::create([
      'user_id' => $request->input('user_id'),
      'type' => '注文フォーム受付',
      'hash' => $hash,
      'from' => $request->input('user_name'),
      'readed' => 0
    ]);

    return response()->json([
      'flag' => true
    ]);
  }

  public function changeOrderFormStatus(Request $request) {
    OrderForm::where('id', $request->input('order_form_id'))->update([
      'is_public' => $request->input('flag')
    ]);

    return response()->json([
      'flag' => true
    ]);
  }
}