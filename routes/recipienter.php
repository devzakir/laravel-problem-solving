<?php
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => 'auth.recipienter'], function () {
  Route::post('user', 'Auth\UserController@current');

  // dashbaord
  Route::post('get_dashboard_data', 'DashboardController@getDashboardData');
  Route::post('get_order_id', 'DashboardController@getOrderId');

  // product
  Route::post('column_prepare', 'ProductController@columnPrepare');
  Route::post('get_column_info', 'ProductController@getColumnInfo');
  Route::post('change_product_column', 'ProductController@changeProductColumn');
  Route::post('save_products', 'ProductController@saveProducts');
  Route::post('get_product_list', 'ProductController@getProductList');
  Route::post('delete_product', 'ProductController@deleteProduct');
  Route::post('get_product_edit_screen_init', 'ProductController@getProductEditScreenInit');
  Route::post('update_products', 'ProductController@updateProducts');

  //order form
  Route::post('create_order_form', 'OrderFormController@createOrderForm');
  Route::post('get_order_form_list', 'OrderFormController@getOrderFormList');
  Route::post('get_order_form_detail', 'OrderFormController@getOrderFormDetail');
  Route::post('append_ordered_form_produts', 'OrderFormController@appendOrderedFormProduct');
  Route::post('cancel_ordered_product', 'OrderFormController@cancelOrderedProduct');
  Route::post('get_order_form_info', 'OrderFormController@getOrderFormInfo');
  Route::post('edit_order_form', 'OrderFormController@editOrderForm');
  Route::post('delete_order_form', 'OrderFormController@deleteOrderForm');
  Route::post('get_message_create_screen_ini', 'OrderFormController@getMessageCreateScreenInit');
  Route::post('get_self_clients', 'OrderFormController@getSelfClients');
  Route::post('create_order_form_message', 'OrderFormController@createOrderFormMessage');
  Route::post('change_order_form_status', 'OrderFormController@changeOrderFormStatus');

  // customer
  Route::post('get_customer_create_init', 'CustomerController@getCustomerCreateInit');
  Route::post('get_customer_edit_init', 'CustomerController@getCustomerEditInit');
  Route::post('invite_user', 'CustomerController@inviteUser');
  Route::post('get_customers', 'CustomerController@getCustomers');
  Route::post('delete_customer', 'CustomerController@deleteCustomer');
  Route::post('update_user', 'CustomerController@updateUser');

  // quote
  Route::post('get_quote_create_init', 'QuoteController@getQuoteCreateInit');
  Route::post('get_client_list', 'QuoteController@getClientList');
  Route::post('save_quote', 'QuoteController@saveQuote');
  Route::post('save_quote_product', 'QuoteController@saveQuoteProduct');
  Route::post('get_quote_list_info', 'QuoteController@getQuoteListInfo');
  Route::post('get_quote_detail_info', 'QuoteController@getQuoteDetailInfo');
  Route::post('send_quote_email', 'QuoteController@sendQuoteEmail');
  Route::post('quote_delete_proc', 'QuoteController@quoteDeleteProc');
  Route::post('get_quote_edit_init', 'QuoteController@getQuoteEditInit');
  Route::post('update_quote', 'QuoteController@updateQuote');

  // setting
  Route::post('get_account_info', 'SettingController@getAccountInfo');
  Route::post('edit_account_info', 'SettingController@editAccountInfo');
  Route::post('save_stamp', 'SettingController@saveStamp');
  Route::post('delete_stamp', 'SettingController@deleteStamp');
  Route::post('edit_account_bank_info', 'SettingController@editAccountBankInfo');
  Route::post('create_user_info', 'SettingController@createUserInfo');
  Route::post('delete_child_proc', 'SettingController@deleteChildProc');
  Route::post('get_child_info', 'SettingController@getChildInfo');
  Route::post('update_user_info', 'SettingController@updateUserInfo');
  Route::post('update_permission', 'SettingController@updatePermission');
  Route::post('get_incomes', 'SettingController@getIncomes');
  Route::post('get_payments', 'SettingController@getPayments');
  Route::post('get_payment_detail', 'SettingController@getPaymentDetail');
  Route::post('refund_proc', 'SettingController@refundProc');

  // order
  Route::post('get_orders_list', 'OrderController@getOrdersList');
  Route::post('get_order_detail', 'OrderController@getOrderDetail');
  Route::post('finish_delivery_card', 'OrderController@finishDeliveryCard');
  Route::post('finish_delivery', 'OrderController@finishDelivery');
  Route::post('finish_multi_delivery', 'OrderController@finishMultiDelivery');
  Route::post('send_order_message', 'OrderController@sendOrderMessage');
  Route::post('send_invoice_message', 'OrderController@sendInvoiceMessage');
  Route::post('get_order_form_detail2', 'OrderController@getOrderFormDetail');
  Route::post('send_order_proc', 'OrderController@sendOrderProc');
  Route::post('save_order_product', 'OrderController@saveOrderProduct');
  Route::post('save_pay_expire', 'OrderController@savePayExpire');
  Route::post('delete_order', 'OrderController@deleteOrder');
  Route::post('finish_multi_invoices', 'OrderController@finishMultiInvoices');

  // mail histories
  Route::post('get_mail_histories', 'MailController@getMailHistories');
  Route::post('get_mail_history_detail', 'MailController@getMailHistoryDetail');

  // shipping
  Route::post('get_deliveried_order', 'OrderController@getDeliveriedOrder');
  Route::post('send_invoice_email', 'OrderController@sendInvoiceEmail');
  Route::post('get_invoces_list', 'OrderController@getInvoicesList');
  Route::post('send_shipping_finish_message', 'OrderController@sendShippingFinishMessage');

  Route::post('logout', 'Auth\LoginController@logout');
});

Route::get('orders/export/csv/{ids}/{recipienter_id}', 'OrderController@csvExporter');
Route::get('orders/export/pdf/{ids}', 'OrderController@pdfExporter');