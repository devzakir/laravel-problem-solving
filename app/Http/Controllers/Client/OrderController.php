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
use App\Models\OrderFormMessage;
use App\Models\News;
use App\Models\OrderFormMailTemplate;
use App\Models\OrderFormMailTemplateProduct;
use App\Models\OrderedProduct;
use App\Models\ProductValue;
use App\Traits\FileUpload;
use App\Models\ProductUserColumn;

class OrderController extends BaseController
{
  use FileUpload;

  public function getOrderForm(Request $request) {
    $user = Auth::user();
    $user_id = $user->id;
    $orderForms = OrderForm::whereIn('id', function($query) use ($user_id) {
      $query->select('order_form_id')
        ->from(with(new OrderFormMessage)->getTable())
        ->where('client_id', $user_id);
    })->with('orders')->get();

    return response()->json([
      'orderForms' => $orderForms
    ]);
  }

  public function orderConfirmProc(Request $request) {
    // $user = Auth::user();

    // OrderFormMailTemplate::where('id', $request->input('id'))->update([
    //   'ordered' => 1
    // ]);

    // re
  }

  public function getOrderFormDetail(Request $request) {
    $user = Auth::user();
    $order_form = OrderForm::where('id', $request->input('id'))->first();
    $order_form_id = $order_form->id;
    $recipienter = Recipienter::where('id', $user->recipienter_id)->first();
    $products = Product::whereIn('id', function($query) use ($order_form_id) {
      $query->select('product_id')
        ->from(with(new OrderedProduct)->getTable())
        ->where('order_form_id', $order_form_id);
    })->with('productValues')->get();
    $columns = ProductColumn::orderBy('order')->get();
    $userColumns = ProductUserColumn::where('recipienter_id', $user->recipienter_id)->with('productColumn')->get();

    $tax_type = ProductUserColumn::where('recipienter_id', $user->recipienter_id)->where('nickname', '発注単価')->first();

    return response()->json([
      'products' => $products,
      'columns' => $columns,
      'userColumns' => $userColumns,
      'order_form' => $order_form,
      'recipienter' => $recipienter,
      'tax_type' => !is_null($tax_type) ? $tax_type->tax_type : 1
    ]);
  }

  public function getOrderDetail(Request $request) {
    $user = Auth::user();
    $order_form = OrderFormMailTemplate::where('id', $request->input('id'))->with('orderForm')->first();
    $order_form_id = $order_form->order_form_id;
    $recipienter = Recipienter::where('id', $user->recipienter_id)->first();
    $products = Product::whereIn('id', function($query) use ($order_form_id) {
      $query->select('product_id')
        ->from(with(new OrderedProduct)->getTable())
        ->where('order_form_id', $order_form_id);
    })->with('productValues')->get();
    $columns = ProductColumn::orderBy('order')->get();
    $userColumns = ProductUserColumn::where('recipienter_id', $user->recipienter_id)->with('productColumn')->get();
    $tax_type = ProductUserColumn::where('recipienter_id', $user->recipienter_id)->where('nickname', '発注単価')->first();
    $order_form_products = OrderFormMailTemplateProduct::where('template_id', $request->input('id'))->with('product')->get();

    return response()->json([
      'products' => $products,
      'columns' => $columns,
      'userColumns' => $userColumns,
      'order_form' => $order_form,
      'recipienter' => $recipienter,
      'order_form_products' => $order_form_products,
      'tax_type' => $tax_type
    ]);
  }

  public function provisionalPayment(Request $request) {
    $user = Auth::user();
    $orderFormMessage = OrderFormMessage::where('order_form_id', $request->input('id'))->where('client_id', $user->id)->first();
    $recipienter = Recipienter::where('id', $orderFormMessage->recipienter_id)->first();
    \Payjp\Payjp::setApiKey('sk_live_c7d3d25dce6894e273b6467870d5a49449feea9147248392900c6b26');
    \Payjp\Charge::create(array(
      "card" => $request->input('token'),
      "amount" => (int) $request->input('price'),
      "currency" => "jpy",
      "tenant" => $recipienter->tenant_id
    ));
    $order = OrderFormMailTemplate::create([
      'recipienter_id' => $orderFormMessage->recipienter_id,
      'user_id' => $user->id,
      'title' => $orderFormMessage->title,
      'content' => $orderFormMessage->message,
      'address_info' => $orderFormMessage->address,
      'order_form_id' => $request->input('id'),
      'payed' => 1,
      'payment_method' => 2
    ]);
    return response()->json([
      'order' => $order
    ]);
  }

  public function bankPaymentProc(Request $request) {
    OrderFormMailTemplate::where('id', $request->input('id'))->update([
      'payed' => 1,
      'payment_method' => 1
    ]);
    $order_form = OrderFormMailTemplate::where('id', $request->input('id'))->with('orderForm')->first();

    News::create([
      'recipienter_id' => $order_form->recipienter_id,
      'type' => '銀行支払い済み',
      'hash' => $order_form->hash,
      'from' => $order_form->user->name,
      'readed' => 0
    ]);

    return response()->json([
      'order_form' => $order_form
    ]);
  }

  public function sendOrderProc(Request $request) {
    $length = 10;
    $token = substr(\str_shuffle('1234567890'), 1, $length);
    $hash = 'XSAD-'.$token;
    $user = Auth::user();
    if (is_null($request->input('order_id'))) {
      $orderFormMessage = OrderFormMessage::where('order_form_id', $request->input('id'))->where('client_id', $user->id)->first();
      $order = OrderFormMailTemplate::create([
        'recipienter_id' => $orderFormMessage->recipienter_id,
        'user_id' => $user->id,
        'title' => $orderFormMessage->title,
        'hash' => $hash,
        'content' => $orderFormMessage->message,
        'address_info' => $orderFormMessage->address,
        'order_form_id' => $request->input('id'),
        'message' => $request->input('message'),
        'deadline' => $request->input('deadline'),
        'deadline_time' => $request->input('deadline_time'),
        'ordered' => 1,
        'price' => $request->input('price'),
        'ordered_at' => Carbon::now(),
        'tax' => $request->input('tax'),
        'tax_type' => $request->input('tax_type'),
      ]);
    } else {
      OrderFormMailTemplate::where('id', $request->input('order_id'))->update([
        'message' => $request->input('message'),
        'deadline' => $request->input('deadline'),
        'deadline_time' => $request->input('deadline_time'),
        'ordered' => 1,
        'price' => $request->input('price'),
        'ordered_at' => Carbon::now(),
        'tax' => $request->input('tax'),
        'tax_type' => $request->input('tax_type'),
      ]);

      $order = OrderFormMailTemplate::where('id', $request->input('order_id'))->first();
    }

    News::create([
      'recipienter_id' => $order->recipienter_id,
      'type' => '新着受注',
      'hash' => $order->hash,
      'from' => $order->user->name,
      'readed' => 0
    ]);

    return response()->json([
      'flag' => true,
      'order' => $order
    ]);
  }

  public function getOrderHistory(Request $request) {
    $user = Auth::user();
    $order_histories = OrderFormMailTemplate::where('user_id', $user->id)->where('ordered', 1)->with('orderForm')->orderByDesc('created_at')->get();

    return response()->json([
      'order_histories' => $order_histories
    ]);
  }

  public function getShippingOrder(Request $request) {
    $user = Auth::user();
    $order_histories = OrderFormMailTemplate::where('user_id', $user->id)->where('delivery', 1)->with('orderForm')->orderByDesc('created_at')->get();

    return response()->json([
      'order_histories' => $order_histories
    ]);
  }

  public function deliveryConfirmProc(Request $request) {
    OrderFormMailTemplate::where('id', $request->input('id'))->update([
      'delivery_confirm' => 1
    ]);

    $order = OrderFormMailTemplate::where('id', $request->input('id'))->first();

    News::create([
      'recipienter_id' => $order->recipienter_id,
      'type' => '受取連絡',
      'hash' => $order->hash,
      'from' => $order->user->name,
      'readed' => 0
    ]);

    $order_form = OrderFormMailTemplate::where('id', $request->input('id'))->with('orderForm')->first();
    return response()->json([
      'order_form' => $order_form
    ]);
  }

  public function saveOrderProduct(Request $request) {
    OrderFormMailTemplateProduct::create([
      'template_id' => $request->input('template_id'),
      'product_id' => $request->input('product_id'),
      'price' => $request->input('price'),
      'amount' => $request->input('amount'),
      'tax_type' => $request->input('tax_type'),
      'tax' => $request->input('tax'),
    ]);

    $user_column = ProductUserColumn::where('recipienter_id', $request->input('recipienter_id'))->where('nickname', '数量')->first();
    $product_value = ProductValue::where('product_id', $request->input('product_id'))->where('product_user_column_id', $user_column->id)->first();
    $product_value->value = $product_value->value - $request->input('amount');
    $product_value->save();

    return response()->json([
      'flag' => true
    ]);
  }
}