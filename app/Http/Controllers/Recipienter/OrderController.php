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
use App\Models\News;
use App\Models\OrderFormMailTemplate;
use App\Models\OrderedProduct;
use App\Models\OrderMessage;
use App\Models\InvoiceMessage;
use App\Models\MailHistory;
use App\Traits\FileUpload;
use App\Models\ProductUserColumn;
use App\Models\OrderFormMailTemplateProduct;
use App\Jobs\OrderFormEmailJob;
use App\Jobs\DeliveryEmailJob;
use App\Models\OrderFormMessage;
use App\Jobs\OrderMessageJob;
use App\Jobs\InvoiceMessageJob;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends BaseController
{
	use FileUpload;

  public function getOrdersList(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $orders = OrderFormMailTemplate::where('recipienter_id', $recipienter->id)->where('ordered', 1)->orderByDesc('created_at')->with('orderForm', 'user', 'recipienter')->get();

    return response()->json([
      'orders' => $orders
    ]);
  }

  public function getOrderDetail(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $order_form = OrderFormMailTemplate::where('id', $request->input('id'))->with('orderForm', 'user', 'recipienter')->first();
    $order_form_id = $order_form->order_form_id;
    $products = Product::whereIn('id', function($query) use ($order_form_id) {
      $query->select('product_id')
        ->from(with(new OrderedProduct)->getTable())
        ->where('order_form_id', $order_form_id);
    })->with('productValues')->get();
    $columns = ProductColumn::orderBy('order')->get();
    $userColumns = ProductUserColumn::where('recipienter_id', $recipienter->id)->with('productColumn')->get();
    $tax_type = ProductUserColumn::where('recipienter_id', $recipienter->id)->where('nickname', '発注単価')->first();
    $order_form_products = OrderFormMailTemplateProduct::where('template_id', $request->input('id'))->with('product')->get();

    return response()->json([
      'products' => $products,
      'columns' => $columns,
      'userColumns' => $userColumns,
      'order_form' => $order_form,
      'recipienter' => $recipienter,
      'tax_type' => $tax_type,
      'order_form_products' => $order_form_products
    ]);
  }
  
  public function deleteOrder(Request $request) {
    OrderFormMailTemplate::where('id', $request->input('id'))->delete();

    return response()->json([
      'flag' => true
    ]);
  }

  public function finishDelivery(Request $request) {
    OrderFormMailTemplate::where('id', $request->input('id'))->update([
      'delivery' => 1,
      'delivery_at' => Carbon::now()
    ]);

    $order = OrderFormMailTemplate::where('id', $request->input('id'))->with('recipienter')->first();

    ClientNews::create([
      'user_id' => $order->user_id,
      'type' => '入荷連絡',
      'hash' => $order->hash,
      'from' => $order->recipienter->name,
      'readed' => 0
    ]);

    return response()->json([
      'flag' => true
    ]);
  }

  public function finishDeliveryCard(Request $request) {
    $length = 10;
    $token = substr(\str_shuffle('1234567890'), 1, $length);
    $hash = 'XSAD-'.$token;
    OrderFormMailTemplate::where('id', $request->input('id'))->update([
      'delivery_card' => 1,
      'delivery_card_at' => Carbon::now(),
      'delivery_hash' => $hash
    ]);

    $order = OrderFormMailTemplate::where('id', $request->input('id'))->with('recipienter')->first();

    // ClientNews::create([
    //   'user_id' => $order->user_id,
    //   'type' => '入荷連絡',
    //   'hash' => $order->hash,
    //   'from' => $order->recipienter->name,
    //   'readed' => 0
    // ]);

    return response()->json([
      'flag' => true
    ]);
  }

  public function finishMultiDelivery(Request $request) {
    $ids = $request->input('ids');
    $length = 10;
    $token = substr(\str_shuffle('1234567890'), 1, $length);
    $hash = 'XSAD-'.$token;
    foreach($ids as $id) {
      $order = OrderFormMailTemplate::where('id', $id)->first();
      if ($order->delivery == 0) {
        $order->delivery_card = 1;
        $order->delivery_card_at = Carbon::now();
        $order->delivery_hash = $hash;
        $order->save();

        // ClientNews::create([
        //   'user_id' => $order->user_id,
        //   'type' => '入荷連絡',
        //   'hash' => $order->hash,
        //   'from' => $order->recipienter->name,
        //   'readed' => 0
        // ]);
      }
    }

    return response()->json([
      'flag' => true
    ]);
  }

  public function getDeliveriedOrder(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $orders = OrderFormMailTemplate::where('recipienter_id', $recipienter->id)->where('delivery_card', 1)->with('orderForm', 'user')->orderByDesc('created_at')->get();

    return response()->json([
      'orders' => $orders
    ]);
  }

  public function sendInvoiceEmail(Request $request) {
    $length = 10;
    $token = substr(\str_shuffle('1234567890'), 1, $length);
    $hash = 'XSAD-'.$token;

    OrderFormMailTemplate::where('id', $request->input('id'))->update([
      'invoiced' => 1,
      'invoice_hash' => $hash,
      'invoice_at' => Carbon::now()
    ]);

    $order = OrderFormMailTemplate::where('id', $request->input('id'))->first();

    ClientNews::create([
      'user_id' => $order->user_id,
      'type' => '請求書',
      'hash' => $order->hash,
      'from' => $order->recipienter->name,
      'readed' => 0
    ]);

    return response()->json([
      'flag' => true
    ]);
  }

  public function getInvoicesList(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $orders = OrderFormMailTemplate::where('recipienter_id', $recipienter->id)->orderByDesc('created_at')->with('orderForm', 'user', 'recipienter', 'products')->get();

    return response()->json([
      'orders' => $orders
    ]);
  }

  public function csvExporter($ids, $recipienter_id) {
    $temp = $ids;
    $temp_arr = \explode(',', $temp);
    $res = [[
      '注文番号',
      '注文日時',
      '注文フォーム',
      '取引先',
      '電話番号',
      '希望納品日',
      '出荷伝票',
      '商品名',
      '数量',
      '税込金額',
      'お届け先住所'
    ]];
    foreach($temp_arr as $item) {
      $order = OrderFormMailTemplate::where('id', $item)->first();
      $order_form_id = $item;
      $products = Product::whereIn('id', function($query) use ($order_form_id) {
        $query->select('product_id')
          ->from(with(new OrderFormMailTemplateProduct)->getTable())
          ->where('template_id', $order_form_id);
      })->get();

      foreach($products as $product) {
        $pro = OrderFormMailTemplateProduct::where('template_id', $order_form_id)->where('product_id', $product->id)->first();
        if ($pro->tax_type == 1) {
          if ($pro->tax == 1) {
            $total_price = (int) $pro->price * $pro->amount * 1.1;
          } else {
            $total_price = (int) $pro->price * $pro->amount * 1.08;
          }
        } else if ($pro->tax_type == 2) {
          $total_price = $pro->price * $pro->amount;
        } else {
          $total_price = $pro->price * $pro->amount;
        }
        $productUserColumn = ProductUserColumn::where('recipienter_id', $recipienter_id)->where('nickname', '商品名')->first();
        $productValue = ProductValue::where('product_id', $product->id)->where('product_user_column_id', $productUserColumn->id)->first();
        \Log::info(is_numeric($pro->amount));
        if ($pro->amount != '') {
          array_push($res, [
            $order->hash,
            $order->ordered_at,
            $order->orderForm->name,
            $order->user->com_name,
            $order->recipienter->phone,
            $order->deadline.' '.$order->deadline_time,
            $order->delivery_card == 1 ? '作成済' : '未作成',
            $productValue->value,
            $pro->amount,
            $total_price,
            $order->recipienter->prefecture.' '.$order->recipienter->city
          ]);
        }
      }
    }
    $exporter = new OrderExport($res);
    return Excel::download($exporter, 'order.csv');
  }

  public function pdfExporter($ids) {
    $temp = $ids;
    $temp_arr = \explode(',', $temp);
    $res = [[
      '注文番号',
      '注文日時',
      '注文フォーム',
      '取引先',
      '希望納品日',
      '出荷伝票',
    ]];
    foreach($temp_arr as $item) {
      $order = OrderFormMailTemplate::where('id', $item)->first();
      array_push($res, [
        $order->hash,
        $order->ordered_at,
        $order->orderForm->name,
        $order->user->name,
        $order->deadline,
        $order->delivery == 1 ? '作成済' : '未作成'
      ]);
    }
    $exporter = new OrderExport($res);
    return Excel::download($exporter, 'order.pdf');
  }

  public function sendOrderMessage(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $orderMessage = OrderMessage::create([
      'recipienter_id' => $request->input('from'),
      'client_id' => $request->input('client_id'),
      'order_id' => $request->input('order_id'),
      'title' => $request->input('title'),
      'message' => $request->input('message'),
      'deadline' => $request->input('deadline'),
      'address' => $request->input('address'),
    ]);

    MailHistory::create([
      'title' => $request->input('title'),
      'user_id' => $request->input('client_id'),
      'recipienter_id' => $recipienter->id,
      'mail_type' => 1,
      'message' => $request->input('message'),
      'deadline' => $request->input('deadline'),
      'address' => $request->input('address')
    ]);

    try {
      OrderMessageJob::dispatch($orderMessage);
    } catch (Exception $e) {
      return false;
    }

    return response()->json([
      'flag' => true
    ]);
  }

  public function sendInvoiceMessage(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    $invoceMessage = InvoiceMessage::create([
      'recipienter_id' => $request->input('from'),
      'client_id' => $request->input('client_id'),
      'order_id' => $request->input('order_id'),
      'title' => $request->input('title'),
      'message' => $request->input('message'),
      'address' => $request->input('address'),
    ]);

    MailHistory::create([
      'title' => $request->input('title'),
      'user_id' => $request->input('client_id'),
      'recipienter_id' => $recipienter->id,
      'mail_type' => 2,
      'message' => $request->input('message'),
      'address' => $request->input('address')
    ]);

    $pdf_url = $this->uploadFile($request->file('pdf'), 'upload');
    \Log::info($pdf_url);

    try {
      InvoiceMessageJob::dispatch($invoceMessage, 'https://ukerundesu.com/storage/'.$pdf_url);
    } catch (Exception $e) {
      return false;
    }

    $length = 10;
    $token = substr(\str_shuffle('1234567890'), 1, $length);
    $hash = 'XSAD-'.$token;

    OrderFormMailTemplate::where('id', $request->input('order_id'))->update([
      'invoiced' => 1,
      'invoice_hash' => $hash,
      'invoice_at' => Carbon::now() 
    ]);

    return response()->json([
      'flag' => true
    ]);
  }

  public function getOrderFormDetail(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();

    $order_form = OrderForm::where('id', $request->input('id'))->first();
    $order_form_id = $order_form->id;
    $recipienter = Recipienter::where('id', $recipienter->id)->first();
    $products = Product::whereIn('id', function($query) use ($order_form_id) {
      $query->select('product_id')
        ->from(with(new OrderedProduct)->getTable())
        ->where('order_form_id', $order_form_id);
    })->with('productValues')->get();
    $columns = ProductColumn::orderBy('order')->get();
    $userColumns = ProductUserColumn::where('recipienter_id', $recipienter->id)->with('productColumn')->get();

    $tax_type = ProductUserColumn::where('recipienter_id', $recipienter->id)->where('nickname', '発注単価')->first();

    return response()->json([
      'products' => $products,
      'columns' => $columns,
      'userColumns' => $userColumns,
      'order_form' => $order_form,
      'recipienter' => $recipienter,
      'tax_type' => $tax_type->tax_type
    ]);
  }

  public function sendOrderProc(Request $request) {
    $length = 10;
    $token = substr(\str_shuffle('1234567890'), 1, $length);
    $hash = 'XSAD-'.$token;
    $user = Auth::user();
    $recipienter = Auth::guard('recipienter')->user();
    if (is_null($request->input('order_id'))) {
      $order = OrderFormMailTemplate::create([
        'recipienter_id' => $recipienter->id,
        'user_id' => $request->input('user_id'),
        'hash' => $hash,
        'title' => '',
        'content' => '',
        'address_info' => '',
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

  public function saveOrderProduct(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    OrderFormMailTemplateProduct::create([
      'template_id' => $request->input('template_id'),
      'product_id' => $request->input('product_id'),
      'price' => $request->input('price'),
      'amount' => $request->input('amount'),
      'tax_type' => $request->input('tax_type'),
      'tax' => $request->input('tax'),
    ]);

    $user_column = ProductUserColumn::where('recipienter_id', $recipienter->id)->where('nickname', '数量')->first();
    $product_value = ProductValue::where('product_id', $request->input('product_id'))->where('product_user_column_id', $user_column->id)->first();
    $product_value->value = $product_value->value - $request->input('amount');
    $product_value->save();

    return response()->json([
      'flag' => true
    ]);
  }

  public function savePayExpire(Request $request) {
    OrderFormMailTemplate::where('id', $request->input('id'))->update([
      'pay_expire' => $request->input('pay_expire')
    ]);
    return response()->json([
      'flag' => true
    ]);
  }

  public function finishMultiInvoices(Request $request) {
    $ids = $request->input('ids');
    foreach($ids as $id) {
      $length = 10;
      $token = substr(\str_shuffle('1234567890'), 1, $length);
      $hash = 'XSAD-'.$token;
      OrderFormMailTemplate::where('id', $id)->update([
        'invoiced' => 1,
        'invoice_at' => Carbon::now(),
        'invoice_hash' => $hash
      ]);
    }

    return response()->json([
      'flag' => true
    ]);
  }

  public function sendShippingFinishMessage(Request $request) {
    $recipienter = Auth::guard('recipienter')->user();
    OrderFormMailTemplate::where('id', $request->input('order_id'))->update([
      'delivery' => 1,
      'delivery_at' => Carbon::now()
    ]);

    $order = OrderFormMailTemplate::where('id', $request->input('order_id'))->with('recipienter')->first();

    ClientNews::create([
      'user_id' => $order->user_id,
      'type' => '入荷連絡',
      'hash' => $order->hash,
      'from' => $order->recipienter->name,
      'readed' => 0
    ]);

    $message = MailHistory::create([
      'title' => $request->input('title'),
      'user_id' => $request->input('client_id'),
      'recipienter_id' => $recipienter->id,
      'mail_type' => 1,
      'message' => $request->input('message'),
      'address' => $request->input('address')
    ]);

    try {
      DeliveryEmailJob::dispatch($message);
    } catch (Exception $e) {
      return false;
    }

    return response()->json([
      'flag' => true
    ]);
  }
}