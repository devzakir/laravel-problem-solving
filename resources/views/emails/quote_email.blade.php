@extends('layouts.email_layout')

@section('content')

<div class="row ">
	<div class="col-md-12 col-sm-12 notification-title">
	<p style="word-break: break-all; white-space: pre-wrap;">
{{ $com_name }} {{ $tanto_name }}様から見積書が届きました。<br/>
見積書のダウンロードは以下から行うことができます。<br/>
<a download href="{{ $url }}">見積書ダウンロード</a><br/>
どうぞよろしくお願いいたします。
  </p>
	</div>
</div>
@endsection