@extends('layouts.email_layout')

@section('content')

<div class="row ">
	<div class="col-md-12 col-sm-12 notification-title">
	<p>
    {{ $com_name }}<br/>
    {{ $name }}様<br/>
    {{ $content }}<br/>
    {{ $address_info }}
  </p>
	</div>
</div>
@endsection