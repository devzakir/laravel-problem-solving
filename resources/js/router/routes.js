function page (path) {
  return () => import(/* webpackChunkName: '' */ `~/pages/${path}`).then(m => m.default || m)
}

export default [
  // 受注者画面
  { path: '/recipient/login', name: 'recipient.auth.login', component: page('recipient/auth/login.vue') },
  { path: '/recipient', name: 'recipient.top', component: page('recipient/index.vue') },

  // 受注管理
  { path: '/recipient/order', name: 'recipient.order1', component: page('recipient/order/index.vue') },
  { path: '/recipient/order/detail', name: 'recipient.order1.detail', component: page('recipient/order/detail.vue') },
  { path: '/recipient/order/create', name: 'recipient.order1.create', component: page('recipient/order/create.vue') },
  { path: '/recipient/order/create_2', name: 'recipient.order1.create_with_address', component: page('recipient/order/create_with_address.vue') },
  { path: '/recipient/order/create_3', name: 'recipient.order1.create_with_detail', component: page('recipient/order/create_with_detail.vue') },
  { path: '/recipient/order/message/create', name: 'recipient.order1.message.create', component: page('recipient/order/message/create.vue') },
  { path: '/recipient/order/message/confirm', name: 'recipient.order1.message.confirm', component: page('recipient/order/message/confirm.vue') },

  // 出荷管理
  { path: '/recipient/shipping', name: 'recipient.shipping', component: page('recipient/shipping/index.vue') },
  { path: '/recipient/shipping/detail', name: 'recipient.shipping.detail', component: page('recipient/shipping/detail.vue') },
  { path: '/recipient/shipping/edit', name: 'recipient.shipping.edit', component: page('recipient/shipping/edit.vue') },
  { path: '/recipient/shipping/message/create', name: 'recipient.shipping.message.create', component: page('recipient/shipping/message/create.vue') },
  
  // 請求書管理
  { path: '/recipient/invoice', name: 'recipient.invoice', component: page('recipient/invoice/index.vue') },
  { path: '/recipient/invoice/detail', name: 'recipient.invoice.detail', component: page('recipient/invoice/detail.vue') },
  { path: '/recipient/invoice/edit', name: 'recipient.invoice.edit', component: page('recipient/invoice/edit.vue') },
  { path: '/recipient/invoice/message/create', name: 'recipient.invoice.message.create', component: page('recipient/invoice/message/create.vue') },
  { path: '/recipient/invoice/message/confirm', name: 'recipient.invoice.message.confirm', component: page('recipient/invoice/message/confirm.vue') },

  // 注文フォーム
  { path: '/recipient/order_form', name: 'recipient.order_form', component: page('recipient/order_form/index.vue') },
  { path: '/recipient/order_form/create', name: 'recipient.order_form.create', component: page('recipient/order_form/create.vue') },
  { path: '/recipient/order_form/edit', name: 'recipient.order_form.edit', component: page('recipient/order_form/edit.vue') },
  { path: '/recipient/order_form/confirm', name: 'recipient.order_form.confirm', component: page('recipient/order_form/confirm.vue') },
  { path: '/recipient/order_form/message/create', name: 'recipient.order_form.message.create', component: page('recipient/order_form/message/create.vue') },
  { path: '/recipient/order_form/message/confirm', name: 'recipient.order_form.message.confirm', component: page('recipient/order_form/message/confirm.vue') },

  // 商品管理
  { path: '/recipient/product', name: 'recipient.product', component: page('recipient/product/index.vue') },
  { path: '/recipient/product/create', name: 'recipient.product.create', component: page('recipient/product/create.vue') },
  { path: '/recipient/product/edit', name: 'recipient.product.edit', component: page('recipient/product/edit.vue') },
  { path: '/recipient/product/column_setting', name: 'recipient.product.column_setting', component: page('recipient/product/column_setting.vue') },

  // 取引先管理
  { path: '/recipient/customer', name: 'recipient.customer', component: page('recipient/customer/index.vue') },
  { path: '/recipient/customer/create', name: 'recipient.customer.create', component: page('recipient/customer/create.vue') },
  { path: '/recipient/customer/edit', name: 'recipient.customer.edit', component: page('recipient/customer/edit.vue') },

  // 見積書管理
  { path: '/recipient/quote', name: 'recipient.quote', component: page('recipient/quote/index.vue') },
  { path: '/recipient/quote/create', name: 'recipient.quote.create', component: page('recipient/quote/create.vue') },
  { path: '/recipient/quote/detail', name: 'recipient.quote.detail', component: page('recipient/quote/detail.vue') },
  { path: '/recipient/quote/edit', name: 'recipient.quote.edit', component: page('recipient/quote/edit.vue') },
  { path: '/recipient/quote/create_2', name: 'recipient.quote.create_2', component: page('recipient/quote/create_2.vue') },
  { path: '/recipient/quote/confirm', name: 'recipient.quote.confirm', component: page('recipient/quote/confirm.vue') },

  // メール送信履歴
  { path: '/recipient/message', name: 'recipient.message', component: page('recipient/message/index.vue') },
  { path: '/recipient/message/detail', name: 'recipient.message.detail', component: page('recipient/message/detail.vue') },

  // アカウント設定
  { path: '/recipient/setting', name: 'recipient.setting', component: page('recipient/setting/index.vue') },
  { path: '/recipient/setting/income', name: 'recipient.setting.income', component: page('recipient/setting/income.vue') },
  { path: '/recipient/setting/payment', name: 'recipient.setting.payment', component: page('recipient/setting/payment.vue') },
  { path: '/recipient/setting/payment_detail', name: 'recipient.setting.payment_detail', component: page('recipient/setting/payment_detail.vue') },

  // 管理画面
  { path: '/admin/login', name: 'admin.auth.login', component: page('admin/auth/login.vue') },

  // ユーザー管理
  { path: '/admin/user', name: 'admin.user', component: page('admin/user/index.vue') },
  { path: '/admin/user/detail', name: 'admin.user.detail', component: page('admin/user/detail.vue') },

  // プラットフォーム
  { path: '/admin/platform', name: 'admin.platform', component: page('admin/platform/index.vue') },

  // 発注者画面
  { path: '/client/login', name: 'client.auth.login', component: page('client/auth/login.vue') },

  // Top画面
  { path: '/client', name: 'client.top', component: page('client/index.vue') },

  { path: '/client/order', name: 'client.order', component: page('client/order/index.vue') },
  { path: '/client/order/detail', name: 'client.order.detail', component: page('client/order/detail.vue') },
  { path: '/client/order/confirm', name: 'client.order.confirm', component: page('client/order/confirm.vue') },
  { path: '/client/order/complete', name: 'client.order.complete', component: page('client/order/complete.vue') },

  { path: '/client/history', name: 'client.history', component: page('client/history/index.vue') },
  { path: '/client/history/detail', name: 'client.history.detail', component: page('client/history/detail.vue') },

  { path: '/client/shipping', name: 'client.shipping', component: page('client/shipping/index.vue') },
  { path: '/client/shipping/detail', name: 'client.shipping.detail', component: page('client/shipping/detail.vue') },
  
  { path: '/client/setting', name: 'client.setting', component: page('client/setting/index.vue') },

  { path: '/account/activate', name: 'account.activate', component: page('auth/activate.vue') },

  { path: '*', component: page('errors/404.vue') }
]
