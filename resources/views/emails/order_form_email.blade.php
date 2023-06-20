@extends('layouts.email_layout')

@section('content')

<div class="row ">
	<div class="col-md-12 col-sm-12 notification-title">
	<p style="word-break: break-all; white-space: pre-wrap;">
{{ $com_name }}<br/>
{{ $name }}様<br/>
{{ $content }}<br/>
<a href="https://ukerundesu.com/client/order/detail?id={{ $order_form_id }}">https://ukerundesu.com/client/order/detail?id={{ $order_form_id }}</a>
{{ $address_info }}
  </p>
	</div>
</div>
@endsection